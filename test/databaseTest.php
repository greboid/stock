<?php
    declare(strict_types=1);

    require './vendor/autoload.php';
    require_once('./configs/test.php');

    use PHPUnit\Framework\TestCase;

    use greboid\stock\Database;

    class DatabaseTest extends TestCase {

        private $pdo;
        private $database;

        protected function setUp() {
            $this->pdo = new PDO('mysql:dbname='.STOCK_DB.';host='.STOCK_DB_HOST, STOCK_DB_USER, STOCK_DB_PW);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

            $this->database = new Database($this->pdo);
        }

        protected function assertPreConditions() {
            $this->assertTrue('MySQL server has gone away' !== $this->pdo->getAttribute(PDO::ATTR_SERVER_INFO),
                              "Issue connecting to the database");
            $this->assertFalse(empty(STOCK_DB),
                               "The test database must exist");
        }

        protected function tearDown() {
            //This seems really really stupid and I'm 100% sure I'll regret it at some point...
            $this->pdo->exec('
                SET FOREIGN_KEY_CHECKS = 0;
                SELECT GROUP_CONCAT(table_schema, \'.\', table_name) INTO @tables
                  FROM information_schema.tables
                  WHERE table_schema = \''.STOCK_DB.'\';

                SET @tables = CONCAT(\'DROP TABLE \', @tables);
                PREPARE stmt FROM @tables;
                EXECUTE stmt;
                DEALLOCATE PREPARE stmt;
                SET FOREIGN_KEY_CHECKS = 1;
            ');
        }

        public function testGetPDO() {
            $this->assertInstanceOf(PDO::class, $this->database->getPDO());
        }

}
