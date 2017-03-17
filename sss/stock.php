<?php
    declare(strict_types=1);

    namespace greboid\stock;

    use \Exception;
    use \mysqli;
    use \mysqli_driver;
    use \Smarty;
    use \greboid\stock\Database;

    class Stock {

        private $database;
        private $dbconnection;

        public function __construct(Database $database = null) {
            if ($database == null) {
                $this->database = new Database();
            } else {
                $this->database = $database;
            }
            $this->dbconnection = $this->database->getConnection();
        }

        public function getSiteName(int $siteID): string {
            if ($siteID == 0) {
                return "All Sites";
            }
            $statement = $this->dbconnection->prepare('SELECT COALESCE((SELECT site_name FROM '.SITES_TABLE.' WHERE site_id=?), "")');
            $statement->bind_param('i', $siteID);
            $statement->execute();
            $statement->bind_result($siteName);
            $statement->fetch();
            return $siteName;
        }

        public function getSiteID(string $siteName): int {
            $siteName = strtolower($siteName);
            if ($siteName == 'all') {
                return 0;
            }
            $statement = $this->dbconnection->prepare('SELECT COALESCE((SELECT site_id FROM '.SITES_TABLE.' WHERE site_name=?), -1)');
            $statement->bind_param('s', $siteName);
            $statement->execute();
            $statement->bind_result($siteID);
            $statement->fetch();
            return $siteID;
        }

        public function getLocationName(int $locationID): string {
            if ($locationID == 0) {
                return "All Locations";
            }
            $statement = $this->dbconnection->prepare('SELECT COALESCE((SELECT location_name FROM '.LOCATIONS_TABLE.' WHERE location_id=?), "")');
            $statement->bind_param('i', $locationID);
            $statement->execute();
            $statement->bind_result($locationName);
            $statement->fetch();
            return $locationName;
        }

        public function getLocationID(string $locationName): int {
            $locationName = strtolower($locationName);
            if ($locationName == 'all') {
                return 0;
            }
            $statement = $this->dbconnection->prepare('SELECT COALESCE((SELECT location_id FROM '.LOCATIONS_TABLE.' WHERE location_name=?), -1)');
            $statement->bind_param('s', $locationName);
            $statement->execute();
            $statement->bind_result($locationID);
            $statement->fetch();
            return $locationID;
        }

        public function getItemName(int $itemID): string {
            $statement = $this->dbconnection->prepare('SELECT COALESCE((SELECT stock_name FROM '.STOCK_TABLE.' WHERE stock_id=?), "")');
            $statement->bind_param('i', $itemID);
            $statement->execute();
            $statement->bind_result($itemName);
            $statement->fetch();
            return $itemName;
        }

        public function getSiteForLocation(int $locationID): string {
            $statement = $this->dbconnection->prepare('SELECT COALESCE((SELECT site_name
                                                FROM '.LOCATIONS_TABLE.'
                                                LEFT JOIN '.SITES_TABLE.' ON '.LOCATIONS_TABLE.'.location_site='.SITES_TABLE.'.site_id
                                                WHERE location_id=?), "")');
            $statement->bind_param('i', $locationID);
            $statement->execute();
            $statement->bind_result($siteName);
            $statement->fetch();
            return $siteName;
        }

        public function getSiteIDForItemID(int $itemID): string {
            $statement = $this->dbconnection->prepare('SELECT COALESCE((SELECT site_id
                FROM '.STOCK_TABLE.'
                LEFT JOIN '.LOCATIONS_TABLE.' ON '.STOCK_TABLE.'.stock_location='.LOCATIONS_TABLE.'.location_id
                LEFT JOIN '.SITES_TABLE.' ON '.LOCATIONS_TABLE.'.location_site='.SITES_TABLE.'.site_id
                WHERE location_id=?), -1)');
            $statement->bind_param('i', $itemID);
            $statement->execute();
            $statement->bind_result($siteID);
            $statement->fetch();
            return $siteID;
        }

        public function getSiteNameForItemID(int $itemID): string {
            $statement = $this->dbconnection->prepare('SELECT COALESCE((SELECT site_name
                FROM '.STOCK_TABLE.'
                LEFT JOIN '.LOCATIONS_TABLE.' ON '.STOCK_TABLE.'.stock_location='.LOCATIONS_TABLE.'.location_id
                LEFT JOIN '.SITES_TABLE.' ON '.LOCATIONS_TABLE.'.location_site='.SITES_TABLE.'.site_id
                WHERE stock_id=?), "")');
            $statement->bind_param('i', $itemID);
            $statement->execute();
            $statement->bind_result($siteName);
            $statement->fetch();
            return $siteName;
        }

        public function getSites(): array {
            $statement = $this->dbconnection->prepare('SELECT site_id, site_name FROM '.SITES_TABLE.' ORDER BY site_name');
            $statement->execute();
            $statement->bind_result($siteID, $siteName);

            $sites = array();
            while ($statement->fetch()) {
                $sites[$siteID] = $siteName;
            }
            return $sites;
        }

        public function getLocations(): array {
            $statement = $this->dbconnection->prepare('SELECT site_id, site_name FROM '.SITES_TABLE);
            $statement->execute();
            $statement->bind_result($id, $name);
            $locations = array();
            while ($statement->fetch()) {
                $locations[$id] = array('name'=>$name, 'locations'=>array());
            }
            $statement->close();
            foreach (array_keys($locations) as $siteid) {
                $statement = $this->dbconnection->prepare('SELECT location_id, location_name
                                           FROM '.LOCATIONS_TABLE.' where location_site=?');
                $statement->bind_param('i', $siteid);
                $statement->execute();
                $statement->bind_result($locationID, $name);
                $locations[$siteid]['locations'] = array();
                while ($statement->fetch()) {
                    $locations[$siteid]['locations'][$locationID] = $name;
                }
            }
            return $locations;
        }

        public function getCategoryName(int $categoryID): string {
            $statement = $this->dbconnection->prepare('SELECT COALESCE((SELECT category_name
                                                      FROM '.CATEGORIES_TABLE.' where category_id=?), "")');
            $statement->bind_param('i', $categoryID);
            $statement->execute();
            $statement->bind_result($categoryName);
            $statement->fetch();
            return $categoryName;
        }

        public function getCategoryID(string $categoryName): int {
            $statement = $this->dbconnection->prepare('SELECT COALESCE((SELECT category_id
                                                      FROM '.CATEGORIES_TABLE.' where category_name=?), -1)');
            $statement->bind_param('s', $categoryName);
            $statement->execute();
            $statement->bind_result($categoryID);
            $statement->fetch();
            return $categoryID;
        }

        public function getCategories(): array {
            $selectClause = 'SELECT categories.category_id AS category_id,
                    COALESCE(parents.category_id, 0) AS category_parent,
                    parents.category_name as category_parent_name,
                    COALESCE(categories.category_name, "") AS category_name
                                                              FROM '.CATEGORIES_TABLE.' as categories
                                                              LEFT JOIN '.CATEGORIES_TABLE.' as parents ON categories.category_parent=parents.category_id';
            $sql = $selectClause.' ORDER BY category_parent, category_name';
            $statement = $this->dbconnection->prepare($sql);
            $statement->execute();
            $statement->bind_result($categoryID, $categoryParent, $categoryParentName, $categoryName);
            $statement->store_result();
            $refs = array();
            $categories = array();
            while ($statement->fetch()) {
                $thisref = &$refs[ $categoryID ];
                $thisref['id'] = $categoryID;
                $thisref['parent'] = $categoryParent;
                $thisref['parentName'] = $categoryParentName;
                $thisref['name'] = $categoryName;
                if ($categoryParent == 0) {
                    $categories[ $categoryID ] = &$thisref;
                } else {
                    $refs[ $categoryParent ]['subcategories'][ $categoryID ] = &$thisref;
                }
            }
            return $categories;
        }

        public function getSubCategories(int $parentCategory): array {
            $sql = 'SELECT categories.category_id AS category_id, parents.category_name AS category_parent, COALESCE(categories.category_name, "") AS category_name
                                                      FROM '.CATEGORIES_TABLE.' as categories
                                                      LEFT JOIN '.CATEGORIES_TABLE.' as parents ON categories.category_parent=parents.category_id
                                                      WHERE categories.category_parent=?
                                                      ORDER BY category_name';
            $statement = $this->dbconnection->prepare($sql);
            $statement->bind_param('i', $parentCategory);
            $statement->execute();
            $statement->bind_result($categoryID, $categoryParent, $categoryName);

            $categories = array();
            while ($statement->fetch()) {
                $categories[$categoryID] = array('name'=>$categoryName, 'parent'=>$categoryParent);
             }
             return $categories;
         }

        public function getLocationStockCounts(): array {
            $statement = $this->dbconnection->prepare('SELECT location_id, location_name FROM '.LOCATIONS_TABLE);
            $statement->execute();
            $statement->bind_result($id, $name);
            $locations = array();
            while ($statement->fetch()) {
                $locations[$name] = array('name'=>$name, 'id'=>$id, 'stockcount'=>0);
            }
            $statement->close();
            foreach ($locations as &$location) {
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

        public function getSiteStock(int $site): array {
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

        public function getLocationStock(int $location): array {
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

        public function getCategoryStock(int $categoryID): array {
            if ($categoryID != 0 && !$this->getCategoryName($categoryID)) {
                throw new Exception('Specified category does not exist.');
            }
            $sql = 'SELECT stock_id, site_name, location_name, stock_name, stock_count, COALESCE(category_name, "") AS category_name
                        FROM '.STOCK_TABLE.'
                        LEFT JOIN '.LOCATIONS_TABLE.' ON '.STOCK_TABLE.'.stock_location='.LOCATIONS_TABLE.'.location_id
                        LEFT JOIN '.SITES_TABLE.' ON '.LOCATIONS_TABLE.'.location_site='.SITES_TABLE.'.site_id
                        LEFT JOIN '.CATEGORIES_TABLE.' ON '.STOCK_TABLE.'.stock_category='.CATEGORIES_TABLE.'.category_id';
            if ($categoryID != 0) {
                $sql .= ' WHERE stock_category=?';
            }
            $statement = $this->dbconnection->prepare($sql);
            if ($categoryID != 0) {
                $statement->bind_param('i', $categoryID);
            }
            $statement->execute();
            $statement->store_result();
            $statement->bind_result($id, $site, $location, $name, $count, $category);
            $stock = array();
            while ($statement->fetch()) {
                $stock[$id] = array('name'=>$name, 'count'=>$count, 'site'=>$site, 'location'=>$location, 'category'=>$category);
            }
            return $stock;
        }

        public function getAllCategoryStock(): array {
            $statement = $this->dbconnection->prepare('SELECT category_id from '.CATEGORIES_TABLE.';');
            $statement->execute();
            $statement->store_result();
            $statement->bind_result($categoryID);
            $stock = array();
            while ($statement->fetch()) {
                $stock[$categoryID] = NULL;
            }
            $statement->close();
            foreach ($stock as $key=>$value) {
                $statement = $this->dbconnection->prepare('SELECT stock_count FROM stock WHERE stock_category=?');
                $statement->bind_param('i', $key);
                $statement->bind_result($stockCount);
                $statement->execute();
                $statement->fetch();
                $stock[$key] = $stockCount;
                $statement->close();
            }
            return $stock;
        }

        public function insertItem(string $name, int $location, int $category, int $count = 0): void {
            $name = strtolower(trim($name));
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
            if (!$this->getCategoryName($category)) {
                throw new Exception('Specified category does not exist.');
            }
            $statement = $this->dbconnection->prepare('INSERT INTO '.STOCK_TABLE.' (stock_name, stock_location, stock_count, stock_category) VALUES (?,?,?,?)');
            $statement->bind_param('siii', $name, $location, $count, $category);
            $statement->execute();
        }

        public function insertLocation(string $name, int $site): void {
            $name = strtolower(trim($name));
            if (empty($name)) {
                throw new Exception('The name cannot be blank.');
            }
            if ($name == 'all') {
                throw new Exception('You cannot use all as a name.');
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

        public function insertSite(string $name): void {
            $name = strtolower(trim($name));
            if (empty($name)) {
                throw new Exception('The name cannot be blank.');
            }
            if ($name == 'all') {
                throw new Exception('You cannot use all as a name.');
            }
            if (preg_match('#\.|\.\.|\\\\|/#', $name)) {
                throw new Exception('The name cannot contain ., .. ,/ or \\');
            }

            $statement = $this->dbconnection->prepare('INSERT INTO '.SITES_TABLE.' (site_name) VALUES (?)');
            $statement->bind_param('s', $name);
            $statement->execute();
        }

        public function insertCategory(string $name, int $parent = 0): void {
            $name = strtolower(trim($name));
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

        public function editItem(int $itemID, int $count): void {
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

        public function deleteSite(int $siteID): void {
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

        public function deleteLocation(int $locationID): void {
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

        public function deleteCategory(int $categoryID): void {
            if (!$this->getCategoryName($categoryID)) {
                throw new Exception('Specified category does not exist.');
            }
            if (count($this->getSubCategories($categoryID)) != 0) {
                throw new Exception('Unable to delete category, it still has sub-categories.');
            }
            if (count($this->getCategoryStock($categoryID)) != 0) {
                throw new Exception('Unable to delete category, it still has items allocated.');
            }
            $statement = $this->dbconnection->prepare('DELETE FROM '.CATEGORIES_TABLE.' WHERE category_id=?');
            $statement->bind_param('i', $categoryID);
            try {
                $statement->execute();
            } catch (Exception $e) {
                throw new Exception('Unable to delete category: '.$e->getMessage());
            }
        }

        public function deleteItem(int $itemID): void {
            if (!$this->getItemName($itemID)) {
                throw new Exception('Specified item does not exist.');
            }
            $statement = $this->dbconnection->prepare('DELETE FROM '.STOCK_TABLE.' WHERE stock_id=?');
            $statement->bind_param('i', $itemID);
            $statement->execute();
        }
    }
