<?php
    declare(strict_types=1);

    namespace greboid\stock;

    use \Exception;
    use \PDO;

    class Database {

        private $pdo;
        private $driver = '';
        private $version = 7;

        public function __construct(PDO $pdo = null) {
            if ($pdo == null) {
                if (!$this->dbConnect()) {
                    throw new Exception('Unable to connect to the database.');
                }
            } else {
                $this->pdo = $pdo;
            }
            $this->driver = $this->pdo->getAttribute(PDO::ATTR_DRIVER_NAME);
            if (!$this->checkVersion()) {
                $this->upgrade();
            }
        }

        public function dbConnect(): bool {
            try {
                $this->pdo = new PDO('mysql:dbname='.STOCK_DB.';host='.STOCK_DB_HOST, STOCK_DB_USER, STOCK_DB_PW);
                $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $this->pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
            } catch (Exception $e) {
                return false;
            }
            return true;
        }

        public function getPDO(): PDO {
            return $this->pdo;
        }

        public function getVersion(): int {
            try {
                $statement = $this->pdo->prepare('
                    SELECT version from '.VERSION_TABLE.' LIMIT 1'
                );
                $statement->execute();
                return $version = $statement->fetchObject()->version;
            } catch (Exception $e) {
                return 0;
            }
        }

        public function checkVersion(): bool {
            return $this->version == $this->getVersion();
        }

        private function execUpgradeSQL(string $sql): string {
            $sql = "SET FOREIGN_KEY_CHECKS=0; ".$sql."; SET FOREIGN_KEY_CHECKS=1;";
            try {
                $this->pdo->exec($sql);
            } catch (Exception $e) {
                return $e->getMessage();
            }
            return '';
        }

        public function dropAndCreate(): string {
            return $this->execUpgradeSQL('
                DROP TABLE IF EXISTS `'.SITES_TABLE.'`;
                DROP TABLE IF EXISTS `'.STOCK_TABLE.'`;
                DROP TABLE IF EXISTS `'.LOCATIONS_TABLE.'`;
                DROP TABLE IF EXISTS `'.VERSION_TABLE.'`;
                DROP TABLE IF EXISTS `'.ACCOUNTS_TABLE.'`;
                DROP TABLE IF EXISTS `'.CATEGORIES_TABLE.'`;
            ');
        }

        public function upgrade(): void {
            $outputs = array();
            $version = $this->getVersion();
            while ($version < $this->version) {
                $currentVersion = $version;
                $version = $version + 1;
                $funcname = 'upgrade'.$currentVersion.'to'.$version;
                $success = $this->$funcname();
                if (!empty($success)) {
                    throw new Exception('Unable to upgrade from '.$currentVersion.'to'.$version.': '.$success);
                }
            }
        }

        public function upgrade0to1(): string {
            return $this->execUpgradeSQL('
                CREATE TABLE IF NOT EXISTS `'.SITES_TABLE.'` (
                    `site_id` int(11) NOT NULL AUTO_INCREMENT,
                    `site_name` varchar(255) NOT NULL,
                    PRIMARY KEY (`site_id`),
                    UNIQUE KEY `site_name` (`site_name`)
                );
                CREATE TABLE IF NOT EXISTS `'.LOCATIONS_TABLE.'` (
                    `location_id` int(11) NOT NULL AUTO_INCREMENT,
                    `location_site` int(11) DEFAULT NULL,
                    `location_name` varchar(255) DEFAULT NULL,
                    PRIMARY KEY (`location_id`),
                    KEY `location_site` (`location_site`),
                    CONSTRAINT `locations-sites` FOREIGN KEY (`location_site`) REFERENCES `'.SITES_TABLE.'` (`site_id`)
                );
                CREATE TABLE IF NOT EXISTS `'.STOCK_TABLE.'` (
                    `stock_id` int(11) NOT NULL AUTO_INCREMENT,
                    `stock_name` varchar(255) NOT NULL,
                    `stock_count` int(11) NOT NULL,
                    `stock_location` int(11) NOT NULL,
                    PRIMARY KEY (`stock_id`),
                    KEY `stock_location` (`stock_location`),
                    CONSTRAINT `stock-locations` FOREIGN KEY (`stock_location`) REFERENCES `'.LOCATIONS_TABLE.'` (`location_id`)
                );
                CREATE TABLE IF NOT EXISTS `'.VERSION_TABLE.'` (
                    `version` int(11) NOT NULL
                );
                INSERT INTO `'.VERSION_TABLE.'` (`version`) values (\'1\');
            ');
        }

        public function upgrade1to2(): string {
            return $this->execUpgradeSQL('
                create table IF NOT EXISTS `'.CATEGORIES_TABLE.'` (
                    `category_id` int (11) NOT null AUTO_INCREMENT,
                    `category_parent` int (11),
                    `category_name` varchar (765),
                    PRIMARY KEY (`category_id`)
                );
                ALTER TABLE `'.STOCK_TABLE.'`
                ADD COLUMN `stock_category` INT(11) null AFTER `stock_location`,
                ADD CONSTRAINT `stock-category` FOREIGN KEY (`stock_category`) REFERENCES `'.CATEGORIES_TABLE.'`(`category_id`);
                UPDATE `version` SET `version` = 2;
            ');
        }

        public function upgrade2to3(): string {
            return $this->execUpgradeSQL('
                CREATE TABLE IF NOT EXISTS `'.ACCOUNTS_TABLE.'` (
                    `id` INT(11) NOT NULL AUTO_INCREMENT,
                    `username` VARCHAR(255) NOT NULL,
                    `password` VARCHAR(255) NOT NULL,
                    `email` VARCHAR(255) NOT NULL,
                    `name` VARCHAR(255) NOT NULL,
                    `active` INT(11) DEFAULT 0,
                    `verified` INT(11) DEFAULT 0,
                    PRIMARY KEY (`id`)
                );
                INSERT INTO `'.ACCOUNTS_TABLE.'` (username, password, email, name, active, verified)
                    VALUES (\'admin\', \''.password_hash('admin', PASSWORD_DEFAULT).'\', \'admin@localhost\', \'Administrator\', 1, 1);
                UPDATE `version` SET `version` = 3;
            ');
        }

        public function upgrade3to4(): string {
            return $this->execUpgradeSQL('
                ALTER TABLE `'.ACCOUNTS_TABLE.'` ADD COLUMN `verify_token` VARCHAR(255) NOT NULL AFTER `verified`, ADD UNIQUE INDEX (`verify_token`);
                UPDATE `version` SET `version` = 4;
            ');
        }

        public function upgrade4to5(): string {
            return $this->execUpgradeSQL('
                ALTER TABLE `'.ACCOUNTS_TABLE.'` ADD UNIQUE INDEX (`email`);
                UPDATE `version` SET `version` = 5;
            ');
        }

        public function upgrade5to6(): string {
            return $this->execUpgradeSQL('
                ALTER TABLE `'.ACCOUNTS_TABLE.'` ADD UNIQUE INDEX `token` (`verify_token`);
                UPDATE `version` SET `version` = 6;
            ');
        }


        public function upgrade6to7(): string {
            return $this->execUpgradeSQL('
                ALTER TABLE `'.STOCK_TABLE.'` ADD COLUMN `stock_min`
                        INT(11) DEFAULT -1 NOT NULL AFTER `stock_category`,
                        ADD COLUMN `stock_max`
                        INT(11) DEFAULT -1 NOT NULL AFTER `stock_min`;
                UPDATE `version` SET `version` = 7;
            ');
        }

    }
