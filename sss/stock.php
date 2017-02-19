<?php

    namespace greboid\stock;

    use \Exception;
    use \mysqli;

    class Stock {
        function dbConnect() {
            mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
            $dbconnection = new mysqli(STOCK_DB_HOST, STOCK_DB_USER, STOCK_DB_PW, STOCK_DB);
            return $dbconnection;
        }

        function getSiteName($siteID) {
            if ($siteID == 0) {
                return "All Sites";
            }
            $dbconnection = $this->dbConnect();
            $statement = $dbconnection->prepare('SELECT site_name FROM '.SITES_TABLE.' WHERE site_id=?');
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
            $dbconnection = $this->dbConnect();
            $statement = $dbconnection->prepare('SELECT site_id FROM '.SITES_TABLE.' WHERE site_name=?');
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
            $dbconnection = $this->dbConnect();
            $statement = $dbconnection->prepare('SELECT location_name FROM '.LOCATIONS_TABLE.' WHERE location_id=?');
            $statement->bind_param('i', $locationID);
            $statement->execute();
            $statement->bind_result($locationName);
            $statement->fetch();
            if ($locationName == NULL) {
                return FALSE;
            }
            return $locationName;
        }

        function getSiteForLocation($locationID) {
            $dbconnection = $this->dbConnect();
            $statement = $dbconnection->prepare('SELECT site_name
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

        function getSites() {
            $dbconnection = $this->dbConnect();
            $statement = $dbconnection->prepare('SELECT site_id, site_name FROM '.SITES_TABLE.' ORDER BY site_name');
            $statement->execute();
            $statement->bind_result($site_id, $site_name);

            $sites = array();
            while ($statement->fetch()) {
                $sites[$site_id] = $site_name;
            }
            return $sites;
        }

        function getLocations() {
            $dbconnection = $this->dbConnect();
            $statement = $dbconnection->prepare('SELECT site_id, site_name FROM '.SITES_TABLE);
            $statement->execute();
            $statement->bind_result($id, $name);
            $locations = array();
            while ($statement->fetch()) {
                $locations[$id] = array('name'=>$name, 'locations'=>array());
            }
            $statement->close();
            foreach(array_keys($locations) as $siteid) {
                $statement = $dbconnection->prepare('SELECT location_id, location_name
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

        function getLocationStockCounts() {
            $dbconnection = $this->dbConnect();
            $statement = $dbconnection->prepare('SELECT location_id, location_name FROM '.LOCATIONS_TABLE);
            $statement->execute();
            $statement->bind_result($id, $name);
            $locations = array();
            while ($statement->fetch()) {
                $locations[$name] = array('name'=>$name, 'id'=>$id, 'stockcount'=>0);
            }
            $statement->close();
            foreach($locations as &$location) {
                $statement = $dbconnection->prepare('SELECT COUNT(*)
                                                    FROM '.STOCK_TABLE.' WHERE stock_location=?');
                $statement->bind_param('i', $location['id']);
                $statement->execute();
                $statement->bind_result($stockcount);
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
            $dbconnection = $this->dbConnect();
            $sql = 'SELECT stock_id, site_name, location_name, stock_name, stock_count
                                  FROM '.STOCK_TABLE.'
                                  LEFT JOIN '.LOCATIONS_TABLE.' ON '.STOCK_TABLE.'.stock_location='.LOCATIONS_TABLE.'.location_id
                                  LEFT JOIN '.SITES_TABLE.' ON '.LOCATIONS_TABLE.'.location_site='.SITES_TABLE.'.site_id';
            if ($site != 0) {
                $sql .= " WHERE site_id=?";
            }
            $sql .= " ORDER BY stock_name, location_name, site_name ASC";
            $statement = $dbconnection->prepare($sql);
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
            $dbconnection = $this->dbConnect();
            $sql = 'SELECT stock_id, site_name, location_name, stock_name, stock_count
                                  FROM '.STOCK_TABLE.'
                                  LEFT JOIN '.LOCATIONS_TABLE.' ON '.STOCK_TABLE.'.stock_location='.LOCATIONS_TABLE.'.location_id
                                  LEFT JOIN '.SITES_TABLE.' ON '.LOCATIONS_TABLE.'.location_site='.SITES_TABLE.'.site_id';
            if ($location != 0) {
                $sql .= " WHERE location_id=?";
            }
            $sql .= " ORDER BY stock_name, location_name, site_name ASC";
            $statement = $dbconnection->prepare($sql);
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
            $dbconnection = $this->dbConnect();
            $statement = $dbconnection->prepare('INSERT INTO '.STOCK_TABLE.' (stock_name, stock_location, stock_count) VALUES (?,?,?)');
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
            $dbconnection = $this->dbConnect();
            $statement = $dbconnection->prepare('INSERT INTO '.LOCATIONS_TABLE.' (location_name, location_site) VALUES (?,?)');
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
            $dbconnection = $this->dbConnect();
            $statement = $dbconnection->prepare('INSERT INTO '.SITES_TABLE.' (site_name) VALUES (?)');
            $statement->bind_param('s', $name);
            $statement->execute();
        }

        function editItem($itemID, $count) {
            if ($count > MAX_STOCK) {
                throw new Exception('Stock count cannot be greater than '.MAX_STOCK);
            }
            if ($count < 0) {
                throw new Exception('Stock count cannot be less than zero.');
            }
            $dbconnection = $this->dbConnect();
            $statement = $dbconnection->prepare('UPDATE '.STOCK_TABLE.' SET stock_count=? where stock_id=?');
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
            $dbconnection = $this->dbConnect();
            $statement = $dbconnection->prepare('DELETE FROM '.SITES_TABLE.' WHERE site_id=?');
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
            $dbconnection = $this->dbConnect();
            $statement = $dbconnection->prepare('DELETE FROM '.LOCATIONS_TABLE.' WHERE location_id=?');
            $statement->bind_param('i', $locationID);
            $statement->execute();
        }
    }
