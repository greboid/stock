<?php
    declare(strict_types=1);

    namespace greboid\stock;

    use \Exception;
    use \greboid\stock\Database;
    use \PDO;

    class Stock {

        private $database;

        public function __construct(Database $database = null) {
            if ($database == null) {
                $this->database = new Database();
            } else {
                $this->database = $database;
            }
        }

        public function getSiteName(int $siteID): string {
            if ($siteID == 0) {
                return "All Sites";
            }
            $statement = $this->database->getPDO()->prepare('
                SELECT COALESCE((SELECT site_name
                FROM '.SITES_TABLE.' WHERE site_id=:siteID), "") as siteID
            ');
            $statement->bindValue(':siteID', $siteID, PDO::PARAM_INT);
            $statement->execute();
            return $statement->fetchObject()->siteID;
        }

        public function getSiteID(string $siteName): int {
            $siteName = strtolower($siteName);
            if ($siteName == 'all') {
                return 0;
            }
            $statement = $this->database->getPDO()->prepare('
                SELECT COALESCE((SELECT site_id
                FROM '.SITES_TABLE.' WHERE site_name=:siteName), -1) as siteName
            ');
            $statement->bindValue(':siteName', $siteName, PDO::PARAM_STR);
            $statement->execute();
            return $statement->fetchObject()->siteName;
        }

        public function getLocationName(int $locationID): string {
            if ($locationID == 0) {
                return "All Locations";
            }
            $statement = $this->database->getPDO()->prepare('
                SELECT COALESCE((SELECT location_name
                FROM '.LOCATIONS_TABLE.' WHERE location_id=:locationID), "") as locationName
            ');
            $statement->bindValue(':locationID', $locationID, PDO::PARAM_INT);
            $statement->execute();
            return $statement->fetchObject()->locationName;
        }

        public function getLocationID(string $locationName): int {
            $locationName = strtolower($locationName);
            if ($locationName == 'all') {
                return 0;
            }
            $statement = $this->database->getPDO()->prepare('
                SELECT COALESCE((SELECT location_id
                FROM '.LOCATIONS_TABLE.' WHERE location_name=:locationName), -1) as locationID
            ');
            $statement->bindValue(':locationName', $locationName, PDO::PARAM_STR);
            $statement->execute();
            return $statement->fetchObject()->locationID;
        }

        public function getItemName(int $itemID): string {
            $statement = $this->database->getPDO()->prepare('
                SELECT COALESCE((SELECT stock_name
                FROM '.STOCK_TABLE.' WHERE stock_id=:stockID), "") as itemName
            ');
            $statement->bindValue(':stockID', $itemID, PDO::PARAM_INT);
            $statement->execute();
            return $statement->fetchObject()->itemName;
        }

        public function getSiteForLocation(int $locationID): string {
            $statement = $this->database->getPDO()->prepare('
                SELECT COALESCE((SELECT site_name
                FROM '.LOCATIONS_TABLE.'
                LEFT JOIN '.SITES_TABLE.' ON '.LOCATIONS_TABLE.'.location_site='.SITES_TABLE.'.site_id
                WHERE location_id=:locationID), "") as siteName
            ');
            $statement->bindValue(':locationID', $locationID, PDO::PARAM_INT);
            $statement->execute();
            return $statement->fetchObject()->siteName;
        }

        public function getSites(): array {
            $statement = $this->database->getPDO()->prepare('
                SELECT site_id, site_name FROM '.SITES_TABLE.' ORDER BY site_name
            ');
            $statement->execute();
            $results = $statement->fetchAll(PDO::FETCH_CLASS);
            $sites = array();
            foreach ($results as $site) {
                $sites[$site->site_id] = $site->site_name;
            }
            return $sites;
        }

        public function getLocations(): array {
            $statement = $this->database->getPDO()->prepare('
                SELECT site_id, site_name FROM '.SITES_TABLE
            );
            $statement->execute();
            $results = $statement->fetchAll(PDO::FETCH_CLASS);
            $locations = array();
            foreach ($results as $result) {
                $locations[$result->site_id] = array('name'=>$result->site_name, 'locations'=>array());
            }
            foreach (array_keys($locations) as $siteid) {
                $statement = $this->database->getPDO()->prepare('
                    SELECT location_id, location_name
                    FROM '.LOCATIONS_TABLE.' where location_site=:siteID
                ');
                $statement->bindValue(':siteID', $siteid, PDO::PARAM_INT);
                $statement->execute();
                $results = $statement->fetchAll(PDO::FETCH_CLASS);
                $locations[$siteid]['locations'] = array();
                foreach ($results as $result) {
                    $locations[$siteid]['locations'][$result->location_id] = $result->location_name;
                }
            }
            return $locations;
        }

        public function getCategoryName(int $categoryID): string {
            $statement = $this->database->getPDO()->prepare('
                SELECT COALESCE((SELECT category_name
                FROM '.CATEGORIES_TABLE.' where category_id=:categoryID), "") as categoryName
            ');
            $statement->bindValue(':categoryID', $categoryID, PDO::PARAM_INT);
            $statement->execute();
            return $statement->fetchObject()->categoryName;
        }

        public function getCategoryID(string $categoryName): int {
            $statement = $this->database->getPDO()->prepare('
                SELECT COALESCE((SELECT category_id
                FROM '.CATEGORIES_TABLE.' where category_name=:categoryName), -1) as categoryID');
            $statement->bindValue(':categoryName', $categoryName, PDO::PARAM_STR);
            $statement->execute();
            return $statement->fetchObject()->categoryID;
        }

        public function getCategories(): array {
            $statement = $this->database->getPDO()->prepare('
                SELECT categories.category_id AS categoryID,
                COALESCE(parents.category_id, null) AS categoryParent,
                parents.category_name as categoryParentName,
                COALESCE(categories.category_name, "") AS categoryName
                FROM '.CATEGORIES_TABLE.' as categories
                LEFT JOIN '.CATEGORIES_TABLE.' as parents ON categories.category_parent=parents.category_id
                ORDER BY categoryParent, categoryName
            ');
            $statement->execute();
            $results = $statement->fetchAll(PDO::FETCH_CLASS);
            $refs = array();
            $categories = array();
            foreach ($results as $result) {
                $thisref = &$refs[ $result->categoryID ];
                $thisref['id'] = $result->categoryID;
                $thisref['parent'] = $result->categoryParent;
                $thisref['parentName'] = $result->categoryParentName;
                $thisref['name'] = $result->categoryName;
                if ($result->categoryParent == null) {
                    $categories[$result->categoryID] = &$thisref;
                } else {
                    $refs[$result->categoryParent]['subcategories'][$result->categoryID] = &$thisref;
                }
            }
            return $categories;
        }

        public function getLocationStockCounts(): array {
            $statement = $this->database->getPDO()->prepare('
                SELECT location_id as id, location_name as name
                FROM '.LOCATIONS_TABLE
            );
            $statement->execute();
            $results = $statement->fetchAll(PDO::FETCH_CLASS);
            $locations = array();
            foreach ($results as $result) {
                $locations[$result->id] = array('name'=>$result->name, 'id'=>$result->id, 'stockcount'=>0);
            }
            foreach ($locations as &$location) {
                $statement = $this->database->getPDO()->prepare('
                    SELECT COUNT(*) as stockcount
                    FROM '.STOCK_TABLE.'
                    WHERE stock_location=:stockLocation
                ');
                $statement->bindValue(':stockLocation', $location['id'], PDO::PARAM_INT);
                $statement->execute();
                $results = $statement->fetchAll(PDO::FETCH_CLASS);
                foreach ($results as $result) {
                    $location['stockcount'] = $result->stockcount;
                    $location['sitename'] = $this->getSiteForLocation($location['id']);
                }
            }
            return $locations;
        }

        public function getSiteStock(int $site): array {
            if (!$this->getSiteName($site)) {
                throw new Exception('Specified site does not exist.');
            }
            $sql = 'SELECT stock_id as id,
                        parents.location_name as site,
                        '.LOCATIONS_TABLE.'.location_name as location,
                        stock_name as name,
                        stock_count as count,
                        stock_min as min,
                        stock_max as max,
                        category_name as category
                    FROM '.STOCK_TABLE.'
                    LEFT JOIN '.LOCATIONS_TABLE.' ON '.STOCK_TABLE.'.stock_location='.LOCATIONS_TABLE.'.location_id
                    LEFT JOIN '.LOCATIONS_TABLE.' as parents ON '.LOCATIONS_TABLE.'.location_site=parents.location_id
                    LEFT JOIN '.CATEGORIES_TABLE.' ON '.STOCK_TABLE.'.stock_category='.CATEGORIES_TABLE.'.category_id';
            if ($site != 0) {
                $sql .= " WHERE site_id=:site";
            }
            $sql .= ' ORDER BY stock_name, '.LOCATIONS_TABLE.'.location_name, parents.location_name ASC';
            $statement = $this->database->getPDO()->prepare($sql);
            if ($site != 0) {
                $statement->bindValue(':site', $site, PDO::PARAM_INT);
            }
            $statement->execute();
            $results = $statement->fetchAll(PDO::FETCH_CLASS);
            $stock = array();
            foreach ($results as $result) {
                $stock[$result->id] =
                    array(
                        'id'=>$result->id,
                        'name'=>$result->name,
                        'count'=>$result->count,
                        'max'=>$result->max,
                        'min'=>$result->min,
                        'site'=>$result->site,
                        'location'=>$result->location,
                        'category'=>$result->category
                        );
            }
            return $stock;
        }

        public function getLocationStock(int $location): array {
            if (!$this->getLocationName($location)) {
                throw new Exception('Specified location does not exist.');
            }
            $sql = 'SELECT stock_id as id, site_name as site, location_name as location, stock_name as name, stock_count as count
                                  FROM '.STOCK_TABLE.'
                                  LEFT JOIN '.LOCATIONS_TABLE.' ON '.STOCK_TABLE.'.stock_location='.LOCATIONS_TABLE.'.location_id
                                  LEFT JOIN '.SITES_TABLE.' ON '.LOCATIONS_TABLE.'.location_site='.SITES_TABLE.'.site_id';
            if ($location != 0) {
                $sql .= " WHERE location_id=:locationID";
            }
            $sql .= " ORDER BY stock_name, location_name, site_name ASC";
            $statement = $this->database->getPDO()->prepare($sql);
            if ($location != 0) {
                $statement->bindValue(':locationID', $location, PDO::PARAM_INT);
            }
            $statement->execute();
            $results = $statement->fetchAll(PDO::FETCH_CLASS);
            $stock = array();
            foreach ($results as $result) {
                $stock[$result->id] = array('name'=>$result->name, 'count'=>$result->count, 'site'=>$result->site, 'location'=>$result->location);
            }
            return $stock;
        }

        public function getCategoryStock(int $categoryID): array {
            if ($categoryID != null && !$this->getCategoryName($categoryID)) {
                throw new Exception('Specified category does not exist.');
            }
            $sql = 'SELECT stock_id as id, site_name as site, location_name as location, stock_name as name, stock_count as count, COALESCE(category_name, "") AS category
                        FROM '.STOCK_TABLE.'
                        LEFT JOIN '.LOCATIONS_TABLE.' ON '.STOCK_TABLE.'.stock_location='.LOCATIONS_TABLE.'.location_id
                        LEFT JOIN '.SITES_TABLE.' ON '.LOCATIONS_TABLE.'.location_site='.SITES_TABLE.'.site_id
                        LEFT JOIN '.CATEGORIES_TABLE.' ON '.STOCK_TABLE.'.stock_category='.CATEGORIES_TABLE.'.category_id';
            if ($categoryID != 0) {
                $sql .= ' WHERE stock_category=:categoryID';
            }
            $statement = $this->database->getPDO()->prepare($sql);
            if ($categoryID != 0) {
                $statement->bindValue(':categoryID', $categoryID, PDO::PARAM_INT);
            }
            $statement->execute();
            $results = $statement->fetchAll(PDO::FETCH_CLASS);
            $stock = array();
            foreach ($results as $result) {
                $stock[$result->id] =
                    array(
                          'name'=>$result->name,
                          'count'=>$result->count,
                          'site'=>$result->site,
                          'location'=>$result->location,
                          'category'=>$result->category
                          );
            }
            return $stock;
        }

        public function getAllCategoryStock(): array {
            $statement = $this->database->getPDO()->prepare('
                SELECT category_id as categoryID
                FROM '.CATEGORIES_TABLE.';
            ');
            $statement->execute();
            $results = $statement->fetchAll(PDO::FETCH_CLASS);
            $stock = array();
            foreach ($results as $result) {
                $stock[$result->categoryID] = null;
            }
            foreach ($stock as $key=>$value) {
                $statement = $this->database->getPDO()->prepare('
                    SELECT COALESCE((SELECT SUM(stock_count)
                    FROM stock
                    WHERE stock_category=:categoryID),0) as stockCount
                ');
                $statement->bindValue(':categoryID', $key, PDO::PARAM_INT);
                $statement->execute();
                $result = $statement->fetchObject()->stockCount;
                $stock[$key] =  $result ;
            }
            return $stock;
        }

        public function hasCategorySubCategories(int $parent): bool {
            $statement = $this->database->getPDO()->prepare('
                SELECT COUNT(*) as count FROM categories
                WHERE category_parent=:categoryParent;
            ');
            $statement->bindValue(':categoryParent', $parent, PDO::PARAM_INT);
            $statement->execute();
            return ($statement->fetchObject()->count > 0);
        }

        public function insertItem(string $name, int $location, int $category, int $count = 0): void {
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
            if (!$this->getCategoryName($category)) {
                throw new Exception('Specified category does not exist.');
            }
            $statement = $this->database->getPDO()->prepare('
                INSERT INTO '.STOCK_TABLE.'
                (stock_name, stock_location, stock_count, stock_category)
                VALUES (:name,:locationID,:count,:categoryID)
                ');
            $statement->bindValue(':name', $name, PDO::PARAM_STR);
            $statement->bindValue(':locationID', $location, PDO::PARAM_INT);
            $statement->bindValue(':count', $count, PDO::PARAM_INT);
            $statement->bindValue(':categoryID', $category, PDO::PARAM_INT);
            $statement->execute();
        }

        public function insertLocation(string $name, int $site): void {
            $name = trim($name);
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

            $statement = $this->database->getPDO()->prepare('
                INSERT INTO '.LOCATIONS_TABLE.'
                (location_name, location_site)
                VALUES (:location,:siteID)
            ');
            $statement->bindValue(':location', $name, PDO::PARAM_STR);
            $statement->bindValue(':siteID', $site, PDO::PARAM_INT);
            $statement->execute();
        }

        public function insertSite(string $name): void {
            $name = trim($name);
            if (empty($name)) {
                throw new Exception('The name cannot be blank.');
            }
            if ($name == 'all') {
                throw new Exception('You cannot use all as a name.');
            }
            if (preg_match('#\.|\.\.|\\\\|/#', $name)) {
                throw new Exception('The name cannot contain ., .. ,/ or \\');
            }

            $statement = $this->database->getPDO()->prepare('
                INSERT INTO '.SITES_TABLE.'
                (site_name)
                VALUES (:siteName)
            ');
            $statement->bindValue(':siteName', $name);
            $statement->execute();
        }

        public function editSite(int $siteID, string $name): void {
            $name = trim($name);
            if (empty($name)) {
                throw new Exception('The name cannot be blank.');
            }
            if ($name == 'all') {
                throw new Exception('You cannot use all as a name.');
            }
            if (preg_match('#\.|\.\.|\\\\|/#', $name)) {
                throw new Exception('The name cannot contain ., .. ,/ or \\');
            }

            $statement = $this->database->getPDO()->prepare('
                UPDATE '.SITES_TABLE.'
                set site_name=:siteName
                WHERE site_id=:siteID
            ');
            $statement->bindValue(':siteName', $name);
            $statement->bindValue(':siteID', $siteID);
            $statement->execute();
        }

        public function editLocation(int $locationID, string $locationName, int $siteID): void {
            $locationName = trim($locationName);
            if (empty($locationName)) {
                throw new Exception('The name cannot be blank.');
            }
            if ($locationName == 'all') {
                throw new Exception('You cannot use all as a name.');
            }
            if (preg_match('#\.|\.\.|\\\\|/#', $locationName)) {
                throw new Exception('The name cannot contain ., .. ,/ or \\');
            }

            $statement = $this->database->getPDO()->prepare('
                UPDATE '.LOCATIONS_TABLE.'
                set location_name=:locationName, location_site=:siteID
                WHERE location_id=:locationID
            ');
            $statement->bindValue(':locationID', $locationID);
            $statement->bindValue(':locationName', $locationName);
            $statement->bindValue(':siteID', $siteID);
            $statement->execute();
        }

        public function editCategory(int $categoryID, string $categoryName, int $categoryParent): void {
            $categoryName = trim($categoryName);
            if (empty($categoryName)) {
                throw new Exception('The name cannot be blank.');
            }
            if ($categoryName == 'all') {
                throw new Exception('You cannot use all as a name.');
            }
            if (preg_match('#\.|\.\.|\\\\|/#', $categoryName)) {
                throw new Exception('The name cannot contain ., .. ,/ or \\');
            }

            $statement = $this->database->getPDO()->prepare('
                UPDATE '.CATEGORIES_TABLE.'
                set category_name=:categoryName, category_parent=:categoryParent
                WHERE category_id=:categoryID
            ');
            if ($categoryParent == 0) {
                $categoryParent = null;
            }
            $statement->bindValue(':categoryID', $categoryID);
            $statement->bindValue(':categoryName', $categoryName);
            $statement->bindValue(':categoryParent', $categoryParent);
            $statement->execute();
        }

        public function editItem(int $itemID, string $itemName, int $locationID, int $categoryID, int $stockCount): void {
            $itemName = trim($itemName);
            if (empty($itemName)) {
                throw new Exception('The name cannot be blank.');
            }
            if ($itemName == 'all') {
                throw new Exception('You cannot use all as a name.');
            }
            if (preg_match('#\.|\.\.|\\\\|/#', $itemName)) {
                throw new Exception('The name cannot contain ., .. ,/ or \\');
            }

            $statement = $this->database->getPDO()->prepare('
                UPDATE '.STOCK_TABLE.'
                set stock_name=:itemName,
                    stock_location=:locationID,
                    stock_category=:categoryID,
                    stock_count=:stockCount
                WHERE stock_id=:itemID
            ');
            $statement->bindValue(':itemID', $itemID, PDO::PARAM_INT);
            $statement->bindValue(':itemName', $itemName, PDO::PARAM_STR);
            $statement->bindValue(':locationID', $locationID, PDO::PARAM_INT);
            $statement->bindValue(':categoryID', $categoryID, PDO::PARAM_INT);
            $statement->bindValue(':stockCount', $stockCount, PDO::PARAM_INT);
            $statement->execute();
        }

        public function insertCategory(string $name, int $parent = 0): void {
            $name = trim($name);
            if (empty($name)) {
                throw new Exception('The name cannot be blank.');
            }
            if (preg_match('#\.|\.\.|\\\\|/#', $name)) {
                throw new Exception('The name cannot contain ., .. ,/ or \\');
            }

            $statement = $this->database->getPDO()->prepare('
                INSERT INTO '.CATEGORIES_TABLE.'
                (category_parent, category_name)
                VALUES (:parentID,:category)
            ');
            if ($parent == 0) {
                $parent = null;
            }
            $statement->bindValue(':parentID', $parent, PDO::PARAM_INT);
            $statement->bindValue(':category', $name, PDO::PARAM_STR);
            $statement->execute();
        }

        public function editItemCount(int $itemID, int $count): void {
            if ($count > MAX_STOCK) {
                throw new Exception('Stock count cannot be greater than '.MAX_STOCK);
            }
            if ($count < 0) {
                throw new Exception('Stock count cannot be less than zero.');
            }
            $statement = $this->database->getPDO()->prepare('
                UPDATE '.STOCK_TABLE.'
                SET stock_count=:stockCount
                WHERE stock_id=:stockID
            ');
            $statement->bindValue(':stockCount', $count, PDO::PARAM_INT);
            $statement->bindValue(':stockID', $itemID, PDO::PARAM_INT);
            $statement->execute();
        }

        public function deleteSite(int $siteID): void {
            if (!$this->getSiteName($siteID)) {
                throw new Exception('Specified site does not exist.');
            }
            if (count($this->getSiteStock($siteID)) != 0) {
                throw new Exception('Unable to delete site, it still contains stock.');
            }
            $statement = $this->database->getPDO()->prepare('
                DELETE FROM '.SITES_TABLE.' WHERE site_id=:siteID
            ');
            $statement->bindValue(':siteID', $siteID, PDO::PARAM_INT);
            $statement->execute();
        }

        public function deleteLocation(int $locationID): void {
            if (!$this->getLocationName($locationID)) {
                throw new Exception('Specified location does not exist.');
            }
            if (count($this->getLocationStock($locationID)) != 0) {
                throw new Exception('Unable to delete location, it still conains stock.');
            }
            $statement = $this->database->getPDO()->prepare('
                DELETE FROM '.LOCATIONS_TABLE.' WHERE location_id=:locationID
            ');
            $statement->bindValue(':locationID', $locationID, PDO::PARAM_INT);
            $statement->execute();
        }

        public function deleteCategory(int $categoryID): void {
            if (!$this->getCategoryName($categoryID)) {
                throw new Exception('Specified category does not exist.');
            }
            if ($this->hasCategorySubCategories($categoryID)) {
                throw new Exception('Unable to delete category, it still has sub-categories.');
            }
            if (count($this->getCategoryStock($categoryID)) != 0) {
                throw new Exception('Unable to delete category, it still has items allocated.');
            }
            $statement = $this->database->getPDO()->prepare('
                DELETE FROM '.CATEGORIES_TABLE.' WHERE category_id=:categoryID
            ');
            $statement->bindValue(':categoryID', $categoryID, PDO::PARAM_INT);
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
            $statement = $this->database->getPDO()->prepare('
                DELETE FROM '.STOCK_TABLE.' WHERE stock_id=:stockID
            ');
            $statement->bindValue(':stockID', $itemID, PDO::PARAM_INT);
            $statement->execute();
        }
    }
