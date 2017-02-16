<?php

    function dbConnect() {
        $dbconnection = new mysqli(STOCK_DB_HOST, STOCK_DB_USER, STOCK_DB_PW, STOCK_DB);
        if($dbconnection->connect_errno > 0){
            die('Unable to connect to database [' . $dbconnection->connect_error . ']');
        }
        return $dbconnection;
    }

    function getSiteName($siteID) {
        if ($siteID == 0) {
            return "All Sites";
        }
        $sites = getSites();
        if (isset($sites[$siteID])) {
            return $sites[$siteID];
        }
        return FALSE;
    }

    function getSites() {
        $dbconnection = dbConnect();
        $statement = $dbconnection->prepare('SELECT site_id, site_name FROM '.SITES_TABLE);
        if (!$statement) {
            die('Unable to prepare sites [' . $dbconnection->error . ']');
        }
        $result = $statement->execute();
        if (!$result) {
            die('Unable to execute sites ['. $dbconnection->error .']');
        }

        $statement->bind_result($site_id, $site_name);

        $sites = array();
        while ($row = $statement->fetch()) {
            $sites[$site_id] = $site_name;
        }
        return $sites;
    }

    function getLocations() {
        $dbconnection = dbConnect();
        $statement = $dbconnection->prepare('SELECT site_id, site_name FROM '.SITES_TABLE);
        if (!$statement) {
            die('Unable to prepare stock sites [' . $dbconnection->error . ']');
        }
        $result = $statement->execute();
        if (!$result) {
            die('Unable to execute sites ['. $dbconnection->error .']');
        }
        $statement->bind_result($id, $name);
        $locations = array();
        while ($row = $statement->fetch()) {
            $locations[$id] = array('name'=>$name, 'locations'=>array());
        }
        $statement->close();
        foreach ($locations as $siteid => $location) {
            $statement = $dbconnection->prepare('SELECT location_id, location_name
                                       FROM '.LOCATIONS_TABLE.' where location_site=?');
            if (!$statement) {
                die('Unable to prepare location select [' . $dbconnection->error . ']');
            }
            if (!$statement->bind_param('i', $siteid)) {
                die('Unable to bind location select [' . $dbconnection->error . ']');
            }
            $result = $statement->execute();
            if (!$result) {
                die('Unable to execute location ['. $dbconnection->error .']');
            }
            $statement->bind_result($location_id, $name);
            $locations[$siteid]['locations'] = array();
            while ($row = $statement->fetch()) {
                $locations[$siteid]['locations'][$location_id] = $name;
            }
        }
        return $locations;
    }

    function getStock($site) {
        $dbconnection = dbConnect();
        $sql = 'SELECT stock_id, site_name, location_name, stock_name, stock_count
                              FROM '.STOCK_TABLE.'
                              LEFT JOIN '.LOCATIONS_TABLE.' ON '.STOCK_TABLE.'.stock_location='.LOCATIONS_TABLE.'.location_id
                              LEFT JOIN '.SITES_TABLE.' ON '.LOCATIONS_TABLE.'.location_site='.SITES_TABLE.'.site_id';
        if ($site != 0) {
            $sql .= " WHERE site_id=?";
        }
        $sql .= " ORDER BY stock_name, location_name, site_name ASC";
        $statement = $dbconnection->prepare($sql);
        if (!$statement) {
            die('Unable to prepare stock select [' . $dbconnection->error . ']');
        }
        if ($site != 0) {
            if (!$statement->bind_param('i', $site)) {
                die('Unable to bind stock select [' . $dbconnection->error . ']');
            }
        }
        $result = $statement->execute();
        if (!$result) {
            die('Unable to execute stock ['. $dbconnection->error .']');
        }
        $statement->bind_result($id, $site, $location, $name, $count);
        $stock = array();
        while ($row = $statement->fetch()) {
            $stock[$id] = array('name'=>$name, 'count'=>$count, 'site'=>$site, 'location'=>$location);
        }
        return $stock;
    }

    function insertItem($name, $location, $count=0) {
        $dbconnection = dbConnect();
        $statement = $dbconnection->prepare('INSERT INTO '.STOCK_TABLE.' (stock_name, stock_location, stock_count) VALUES (?,?,?)');
        if (!$statement) {
            die('Unable to prepare stock insert');
        }
        if (!$statement->bind_param('sii', $name, $location, $count)) {
            die('Unable to bind stock insert.');
        }
        $result = $statement->execute();
        if (!$result) {
            die ('Unable to execute stock insert.');
        }
    }

    function insertLocation($name, $site) {
        $dbconnection = dbConnect();
        $statement = $dbconnection->prepare('INSERT INTO '.LOCATIONS_TABLE.' (location_name, location_site) VALUES (?,?)');
        if (!$statement) {
            die('Unable to prepare location insert');
        }
        if (!$statement->bind_param('si', $name, $site)) {
            die('Unable to bind location insert.');
        }
        $result = $statement->execute();
        if (!$result) {
            die ('Unable to execute location insert.');
        }
    }

    function insertSite($name) {
        $dbconnection = dbConnect();
        $statement = $dbconnection->prepare('INSERT INTO '.SITES_TABLE.' (site_name) VALUES (?)');
        if (!$statement) {
            die('Unable to prepare site insert');
        }
        if (!$statement->bind_param('s', $name)) {
            die('Unable to bind site insert.');
        }
        $result = $statement->execute();
        if (!$result) {
            die ('Unable to execute site insert.');
        }
    }

    function editItem($itemID, $count) {
        $dbconnection = dbConnect();
        $statement = $dbconnection->prepare('UPDATE '.STOCK_TABLE.' SET stock_count=? where stock_id=?');
        if (!$statement) {
            die('Unable to prepare stock update');
        }
        if (!$statement->bind_param('ii', $count, $itemID)) {
            die('Unable to bind stock update.');
        }
        $result = $statement->execute();
        if (!$result) {
            die ('Unable to execute stock update.');
        }
    }
