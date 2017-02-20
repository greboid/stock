<?php

    namespace greboid\stock;

    use \Exception;
    use \mysqli;

    class Stock {

        private $dbconnection;
        private $version = 1;

        function __construct() {
            if (!$this->dbConnect()) {
                throw new Exception('Unable to connect to the database.');
            }
        }

        function dbConnect() {
            mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
            try {
                $this->dbconnection = new mysqli(STOCK_DB_HOST, STOCK_DB_USER, STOCK_DB_PW, STOCK_DB);
            } catch (Exception $e) {
                return FALSE;
            }
            return TRUE;
        }

        function getVersion() {
            try {
                $statement = $this->dbconnection->prepare('SELECT version from '.VERSION_TABLE);
                $statement->execute();
                $statement->bind_result($version);
                $statement->fetch();
            } catch (Exception $e) {
                return FALSE;
            }
            return $version;
        }

        function checkVersion() {
            return $this->version == $this->getVersion();
        }

        function getSiteName($siteID) {
            if ($siteID == 0) {
                return "All Sites";
            }
            $statement = $this->dbconnection->prepare('SELECT site_name FROM '.SITES_TABLE.' WHERE site_id=?');
            $statement->bind_param('i', $siteID);
            $statement->execute();
            $statement->bind_result($siteName);
            $statement->fetch();
            if ($siteName == NULL) {
                return FALSE;
            }
            return $siteName;
        }

        function getSiteID($siteName) {
            $siteName = strtolower($siteName);
            if ($siteName == 'all') {
                return 0;
            }
            $statement = $this->dbconnection->prepare('SELECT site_id FROM '.SITES_TABLE.' WHERE site_name=?');
            $statement->bind_param('s', $siteName);
            $statement->execute();
            $statement->bind_result($siteID);
            $statement->fetch();
            if ($siteID == NULL) {
                return FALSE;
            }
            return $siteID;
        }

        function getLocationName($locationID) {
            $statement = $this->dbconnection->prepare('SELECT location_name FROM '.LOCATIONS_TABLE.' WHERE location_id=?');
            $statement->bind_param('i', $locationID);
            $statement->execute();
            $statement->bind_result($locationName);
            $statement->fetch();
            if ($locationName == NULL) {
                return FALSE;
            }
            return $locationName;
        }

        function getLocationID($locationName) {
            $statement = $this->dbconnection->prepare('SELECT location_id FROM '.LOCATIONS_TABLE.' WHERE location_name=?');
            $statement->bind_param('s', $locationName);
            $statement->execute();
            $statement->bind_result($locationID);
            $statement->fetch();
            if ($locationID == NULL) {
                return FALSE;
            }
            return $locationID;
        }

        function getItemName($itemID) {
            $statement = $this->dbconnection->prepare('SELECT stock_name FROM '.STOCK_TABLE.' WHERE stock_id=?');
            $statement->bind_param('i', $itemID);
            $statement->execute();
            $statement->bind_result($itemName);
            $statement->fetch();
            if ($itemName == NULL) {
                return FALSE;
            }
            return $itemName;
        }

        function getSiteForLocation($locationID) {
            $statement = $this->dbconnection->prepare('SELECT site_name
                                                FROM '.LOCATIONS_TABLE.'
                                                LEFT JOIN '.SITES_TABLE.' ON '.LOCATIONS_TABLE.'.location_site='.SITES_TABLE.'.site_id
                                                WHERE location_id=?');
            $statement->bind_param('i', $locationID);
            $statement->execute();
            $statement->bind_result($siteName);
            $statement->fetch();
            if ($siteName == NULL) {
                return FALSE;
            }
            return $siteName;
        }

        function getSiteIDForItemID($itemID) {
            $statement = $this->dbconnection->prepare('
                SELECT site_id
                FROM '.STOCK_TABLE.'
                LEFT JOIN '.LOCATIONS_TABLE.' ON '.STOCK_TABLE.'.stock_location='.LOCATIONS_TABLE.'.location_id
                LEFT JOIN '.SITES_TABLE.' ON '.LOCATIONS_TABLE.'.location_site='.SITES_TABLE.'.site_id
                WHERE location_id=?');
            $statement->bind_param('i', $itemID);
            $statement->execute();
            $statement->bind_result($siteID);
            $statement->fetch();
            if ($siteID == NULL) {
                return FALSE;
            }
            return $siteID;
        }

        function getSiteNameForItemID($itemID) {
            $statement = $this->dbconnection->prepare('
                SELECT site_name
                FROM '.STOCK_TABLE.'
                LEFT JOIN '.LOCATIONS_TABLE.' ON '.STOCK_TABLE.'.stock_location='.LOCATIONS_TABLE.'.location_id
                LEFT JOIN '.SITES_TABLE.' ON '.LOCATIONS_TABLE.'.location_site='.SITES_TABLE.'.site_id
                WHERE location_id=?');
            $statement->bind_param('i', $itemID);
            $statement->execute();
            $statement->bind_result($siteName);
            $statement->fetch();
            if ($siteName == NULL) {
                return FALSE;
            }
            return $siteName;
        }

        function getSites() {
            $statement = $this->dbconnection->prepare('SELECT site_id, site_name FROM '.SITES_TABLE.' ORDER BY site_name');
            $statement->execute();
            $statement->bind_result($site_id, $site_name);

            $sites = array();
            while ($statement->fetch()) {
                $sites[$site_id] = $site_name;
            }
            return $sites;
        }

        function getLocations() {
            $statement = $this->dbconnection->prepare('SELECT site_id, site_name FROM '.SITES_TABLE);
            $statement->execute();
            $statement->bind_result($id, $name);
            $locations = array();
            while ($statement->fetch()) {
                $locations[$id] = array('name'=>$name, 'locations'=>array());
            }
            $statement->close();
            foreach(array_keys($locations) as $siteid) {
                $statement = $this->dbconnection->prepare('SELECT location_id, location_name
                                           FROM '.LOCATIONS_TABLE.' where location_site=?');
                $statement->bind_param('i', $siteid);
                $statement->execute();
                $statement->bind_result($location_id, $name);
                $locations[$siteid]['locations'] = array();
                while ($statement->fetch()) {
                    $locations[$siteid]['locations'][$location_id] = $name;
                }
            }
            return $locations;
        }

        function getCategories() {
            $statement = $this->dbconnection->prepare('SELECT category_id, category_name FROM '.CATEGORIES_TABLE.' ORDER BY category_name');
            $statement->execute();
            $statement->bind_result($category_id, $category_name);

            $categories = array();
            while ($statement->fetch()) {
                $categories[$category_id] = $category_name;
            }
            return $categories;
        }

        function getLocationStockCounts() {
            $statement = $this->dbconnection->prepare('SELECT location_id, location_name FROM '.LOCATIONS_TABLE);
            $statement->execute();
            $statement->bind_result($id, $name);
            $locations = array();
            while ($statement->fetch()) {
                $locations[$name] = array('name'=>$name, 'id'=>$id, 'stockcount'=>0);
            }
            $statement->close();
            foreach($locations as &$location) {
                $statement = $this->dbconnection->prepare('SELECT COUNT(*)
                                                    FROM '.STOCK_TABLE.' WHERE stock_location=?');
                $statement->bind_param('i', $location['id']);
                $statement->execute();
                $statement->bind_result($stockcount);
                $statement->store_result();
                while ($statement->fetch()) {
                    $location['stockcount'] = $stockcount;
                    $location['sitename'] = $this->getSiteForLocation($location['id']);
                }
                $statement->close();
            }
            return $locations;
        }

        function getSiteStock($site) {
            if (!$this->getSiteName($site)) {
                throw new Exception('Specified site does not exist.');
            }
            $sql = 'SELECT stock_id, site_name, location_name, stock_name, stock_count
                                  FROM '.STOCK_TABLE.'
                                  LEFT JOIN '.LOCATIONS_TABLE.' ON '.STOCK_TABLE.'.stock_location='.LOCATIONS_TABLE.'.location_id
                                  LEFT JOIN '.SITES_TABLE.' ON '.LOCATIONS_TABLE.'.location_site='.SITES_TABLE.'.site_id';
            if ($site != 0) {
                $sql .= " WHERE site_id=?";
            }
            $sql .= " ORDER BY stock_name, location_name, site_name ASC";
            $statement = $this->dbconnection->prepare($sql);
            if ($site != 0) {
                $statement->bind_param('i', $site);
            }
            $statement->execute();
            $statement->bind_result($id, $site, $location, $name, $count);
            $stock = array();
            while ($statement->fetch()) {
                $stock[$id] = array('name'=>$name, 'count'=>$count, 'site'=>$site, 'location'=>$location);
            }
            return $stock;
        }

        function getLocationStock($location) {
            if (!$this->getLocationName($location)) {
                throw new Exception('Specified location does not exist.');
            }
            $sql = 'SELECT stock_id, site_name, location_name, stock_name, stock_count
                                  FROM '.STOCK_TABLE.'
                                  LEFT JOIN '.LOCATIONS_TABLE.' ON '.STOCK_TABLE.'.stock_location='.LOCATIONS_TABLE.'.location_id
                                  LEFT JOIN '.SITES_TABLE.' ON '.LOCATIONS_TABLE.'.location_site='.SITES_TABLE.'.site_id';
            if ($location != 0) {
                $sql .= " WHERE location_id=?";
            }
            $sql .= " ORDER BY stock_name, location_name, site_name ASC";
            $statement = $this->dbconnection->prepare($sql);
            if ($location != 0) {
                $statement->bind_param('i', $location);
            }
            $statement->execute();
            $statement->bind_result($id, $site, $location, $name, $count);
            $stock = array();
            while ($statement->fetch()) {
                $stock[$id] = array('name'=>$name, 'count'=>$count, 'site'=>$site, 'location'=>$location);
            }
            return $stock;
        }

        function insertItem($name, $location, $count=0) {
            $name = trim($name);
            if (empty($name)) {
                throw new Exception('The name cannot be blank.');
            }
            if (preg_match('#\.|\.\.|\\\\|/#', $name)) {
                throw new Exception('The name cannot contain ., .. ,/ or \\');
            }
            if ($count > MAX_STOCK) {
                throw new Exception('Stock count cannot be greater than '.MAX_STOCK);
            }
            if ($count < 0) {
                throw new Exception('Stock count cannot be less than zero.');
            }
            if (!$this->getLocationName($location)) {
                throw new Exception('Specified location does not exist.');
            }
            $statement = $this->dbconnection->prepare('INSERT INTO '.STOCK_TABLE.' (stock_name, stock_location, stock_count) VALUES (?,?,?)');
            $statement->bind_param('sii', $name, $location, $count);
            $statement->execute();
        }

        function insertLocation($name, $site) {
            $name = trim($name);
            if (empty($name)) {
                throw new Exception('The name cannot be blank.');
            }
            if (preg_match('#\.|\.\.|\\\\|/#', $name)) {
                throw new Exception('The name cannot contain ., .. ,/ or \\');
            }
            if (!$this->getSiteName($site)) {
                throw new Exception('Specified site does not exist.');
            }

            $statement = $this->dbconnection->prepare('INSERT INTO '.LOCATIONS_TABLE.' (location_name, location_site) VALUES (?,?)');
            $statement->bind_param('si', $name, $site);
            $statement->execute();
        }

        function insertSite($name) {
            $name = trim($name);
            if (empty($name)) {
                throw new Exception('The name cannot be blank.');
            }
            if (preg_match('#\.|\.\.|\\\\|/#', $name)) {
                throw new Exception('The name cannot contain ., .. ,/ or \\');
            }

            $statement = $this->dbconnection->prepare('INSERT INTO '.SITES_TABLE.' (site_name) VALUES (?)');
            $statement->bind_param('s', $name);
            $statement->execute();
        }

        function insertCategory($name, $parent=0) {
            $name = trim($name);
            if (empty($name)) {
                throw new Exception('The name cannot be blank.');
            }
            if (preg_match('#\.|\.\.|\\\\|/#', $name)) {
                throw new Exception('The name cannot contain ., .. ,/ or \\');
            }

            $statement = $this->dbconnection->prepare('INSERT INTO '.CATEGORIES_TABLE.' (category_parent, category_name) VALUES (?,?)');
            $statement->bind_param('is', $parent, $name);
            $statement->execute();
        }

        function editItem($itemID, $count) {
            if ($count > MAX_STOCK) {
                throw new Exception('Stock count cannot be greater than '.MAX_STOCK);
            }
            if ($count < 0) {
                throw new Exception('Stock count cannot be less than zero.');
            }
            $statement = $this->dbconnection->prepare('UPDATE '.STOCK_TABLE.' SET stock_count=? where stock_id=?');
            $statement->bind_param('ii', $count, $itemID);
            $statement->execute();
        }

        function deleteSite($siteID) {
            if (!$this->getSiteName($siteID)) {
                throw new Exception('Specified site does not exist.');
            }
            if (count($this->getSiteStock($siteID)) != 0) {
                throw new Exception('Unable to delete site, it still conains stock.');
            }
            $statement = $this->dbconnection->prepare('DELETE FROM '.SITES_TABLE.' WHERE site_id=?');
            $statement->bind_param('i', $siteID);
            $statement->execute();
        }

        function deleteLocation($locationID) {
            if (!$this->getLocationName($locationID)) {
                throw new Exception('Specified location does not exist.');
            }
            if (count($this->getLocationStock($locationID)) != 0) {
                throw new Exception('Unable to delete location, it still conains stock.');
            }
            $statement = $this->dbconnection->prepare('DELETE FROM '.LOCATIONS_TABLE.' WHERE location_id=?');
            $statement->bind_param('i', $locationID);
            $statement->execute();
        }

        function deleteItem($itemID) {
            if (!$this->getItemName($itemID)) {
                throw new Exception('Specified item does not exist.');
            }
            $statement = $this->dbconnection->prepare('DELETE FROM '.STOCK_TABLE.' WHERE stock_id=?');
            $statement->bind_param('i', $itemID);
            $statement->execute();
        }

        function dropAndCreate() {
            $statement = $this->dbconnection->multi_query('
                SET FOREIGN_KEY_CHECKS=0;
                DROP TABLE IF EXISTS `'.SITES_TABLE.'`;
                DROP TABLE IF EXISTS `'.STOCK_TABLE.'`;
                DROP TABLE IF EXISTS `'.LOCATIONS_TABLE.'`;
                DROP TABLE IF EXISTS `'.VERSION_TABLE.'`;
                CREATE TABLE `'.SITES_TABLE.'` (
                  `site_id` int(11) NOT NULL AUTO_INCREMENT,
                  `site_name` varchar(255) NOT NULL,
                  PRIMARY KEY (`site_id`),
                  UNIQUE KEY `site_name` (`site_name`)
                );
                CREATE TABLE `'.LOCATIONS_TABLE.'` (
                  `location_id` int(11) NOT NULL AUTO_INCREMENT,
                  `location_site` int(11) DEFAULT NULL,
                  `location_name` varchar(255) DEFAULT NULL,
                  PRIMARY KEY (`location_id`),
                  KEY `location_site` (`location_site`),
                  CONSTRAINT `locations-sites` FOREIGN KEY (`location_site`) REFERENCES `'.SITES_TABLE.'` (`site_id`)
                );
                CREATE TABLE `'.STOCK_TABLE.'` (
                  `stock_id` int(11) NOT NULL AUTO_INCREMENT,
                  `stock_name` varchar(255) NOT NULL,
                  `stock_count` int(11) NOT NULL,
                  `stock_location` int(11) NOT NULL,
                  PRIMARY KEY (`stock_id`),
                  KEY `stock_location` (`stock_location`),
                  CONSTRAINT `stock-locations` FOREIGN KEY (`stock_location`) REFERENCES `'.LOCATIONS_TABLE.'` (`location_id`)
                );
                create table `'.CATEGORIES_TABLE.'` (
                    `category_id` int (11) NOT NULL AUTO_INCREMENT,
                    `category_parent` int (11),
                    `category_name` varchar (765),
                      PRIMARY KEY (`category_id`)
                );
                CREATE TABLE `'.VERSION_TABLE.'` (
                  `version` int(11) NOT NULL
                );
                INSERT INTO `'.VERSION_TABLE.'`(`version`) values (1);
                SET FOREIGN_KEY_CHECKS=1;
                ');
            while ($this->dbconnection->next_result()) {;}
        }

        function upgrade() {
            $outputs = array();
            $version = $this->getVersion();
            while ($version < $this->version) {
                $funcname = 'upgrade'.$version.'to'.++$version;
                $outputs[] = $this->$funcname();
            }
            return $outputs;
        }

        function upgrade0to1() {
            try {
                $statement = $this->dbconnection->multi_query("
                    create table `".CATEGORIES_TABLE."` (
                        `category_id` int (11) NOT NULL AUTO_INCREMENT,
                        `category_parent` int (11),
                        `category_name` varchar (765),
                          PRIMARY KEY (`category_id`)
                    );
                    ALTER TABLE `".STOCK_TABLE."`
                    ADD COLUMN `stock_category` INT(11) NULL AFTER `stock_location`,
                    ADD CONSTRAINT `stock-category` FOREIGN KEY (`stock_category`) REFERENCES `".CATEGORIES_TABLE."`(`category_id`);
                    UPDATE `version` SET `version` = '1';
                ");
                while ($this->dbconnection->next_result()) {;}
            } catch (Exception $e) {
                return FALSE;
            }
            return TRUE;

        }
    }
