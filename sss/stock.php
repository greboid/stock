<?php

    namespace greboid\stock;

    class Stock {
        function dbConnect() {
            mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
            $dbconnection = new \mysqli(STOCK_DB_HOST, STOCK_DB_USER, STOCK_DB_PW, STOCK_DB);
            return $dbconnection;
        }

        function getSiteName($siteID) {
            if ($siteID == 0) {
                return "All Sites";
            }
            $sites = $this->getSites();
            if (isset($sites[$siteID])) {
                return $sites[$siteID];
            }
            return FALSE;
        }

        function getSites() {
            $dbconnection = $this->dbConnect();
            $statement = $dbconnection->prepare('SELECT site_id, site_name FROM '.SITES_TABLE);
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

        function getStock($site) {
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

        function insertItem($name, $location, $count=0) {
            $dbconnection = $this->dbConnect();
            $statement = $dbconnection->prepare('INSERT INTO '.STOCK_TABLE.' (stock_name, stock_location, stock_count) VALUES (?,?,?)');
            $statement->bind_param('sii', $name, $location, $count);
            $statement->execute();
        }

        function insertLocation($name, $site) {
            $dbconnection = $this->dbConnect();
            $statement = $dbconnection->prepare('INSERT INTO '.LOCATIONS_TABLE.' (location_name, location_site) VALUES (?,?)');
            $statement->bind_param('si', $name, $site);
            $statement->execute();
        }

        function insertSite($name) {
            $dbconnection = $this->dbConnect();
            $statement = $dbconnection->prepare('INSERT INTO '.SITES_TABLE.' (site_name) VALUES (?)');
            $statement->bind_param('s', $name);
            $statement->execute();
        }

        function editItem($itemID, $count) {
            $dbconnection = $this->dbConnect();
            $statement = $dbconnection->prepare('UPDATE '.STOCK_TABLE.' SET stock_count=? where stock_id=?');
            $statement->bind_param('ii', $count, $itemID);
            $statement->execute();
        }
    }
