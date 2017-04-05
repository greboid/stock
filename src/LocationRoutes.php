<?php
    declare(strict_types=1);

    namespace greboid\stock;

    use \Exception;
    use \greboid\stock\Stock;
    use \Bramus\Router\Router;
    use \Smarty;
    use \ICanBoogie\Storage\RunTimeStorage;

    class LocationRoutes {

        public function addRoutes(RunTimeStorage $storage): void {
            $app = $storage->retrieve('app');
            $smarty = $storage->retrieve('smarty');
            $stock = $storage->retrieve('stock');

            $app->get('/locations/', function() use ($smarty, $stock) {
                return $smarty->fetch('locations.tpl');
            });
            $app->get('/location/{locationName}', function($locationName) use ($smarty, $stock) {
                $locationName = filter_var($locationName, FILTER_UNSAFE_RAW);
                $locationid = $stock->getLocationID($locationName);
                if ($locationid === false) {
                    header('HTTP/1.1 404 Not Found');
                    return $smarty->fetch('404.tpl');
                }
                try {
                    if ($stock->getLocationName($locationid) !== false) {
                        $smarty->assign('locationid', $locationid);
                        $smarty->assign('site', $stock->getLocationName($locationid));
                        $smarty->assign('stock', $stock->getLocationStock($locationid));
                        return $smarty->fetch('stock.tpl');
                    } else {
                        header('HTTP/1.1 404 Not Found');
                        return $smarty->fetch('404.tpl');
                    }
                } catch (Exception $e) {
                    $smarty->assign('error', $e->getMessage());
                    return $smarty->fetch('500.tpl');
                }
            });
            $app->get('/add/location', function() use ($smarty, $stock, $app) {
                if (count($stock->getLocations()) == 0) {
                    return $app->redirect('/add/site');
                }
                try {
                    return $smarty->fetch('addlocation.tpl');
                } catch (Exception $e) {
                    $smarty->assign('error', $e->getMessage());
                    return $smarty->fetch('500.tpl');
                }
            });
            $app->post('/add/location', function() use ($smarty, $stock, $app) {
                try {
                    $name = filter_input(INPUT_POST, "name", FILTER_UNSAFE_RAW, FILTER_NULL_ON_FAILURE);
                    $site = filter_input(INPUT_POST, "site", FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE);
                    if ($name !== false && $site !== false) {
                        $stock->insertLocation($name, $site);
                    } else {
                        $smarty->assign('error', 'Missing required value.');
                        return $smarty->fetch('500.tpl');
                    }
                    return $app->redirect('/manage/locations');
                } catch (Exception $e) {
                    $smarty->assign('error', $e->getMessage());
                    return $smarty->fetch('500.tpl');
                }
            });
            $app->post('/edit/location', function() use ($smarty, $stock, $app) {
                try {
                    $locationID = filter_input(INPUT_POST, "editID", FILTER_VALIDATE_INT);
                    $locationName = filter_input(INPUT_POST, "editName", FILTER_UNSAFE_RAW, FILTER_NULL_ON_FAILURE);
                    $siteID = filter_input(INPUT_POST, "editSite", FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE);
                    if ($locationName !== false) {
                        $stock->editLocation($locationID, $locationName, $siteID);
                    } else {
                        $smarty->assign('error', 'Missing required value.');
                        return $smarty->fetch('500.tpl');
                    }
                    return $app->redirect('/manage/locations');
                } catch (Exception $e) {
                    $smarty->assign('error', $e->getMessage());
                    return $smarty->fetch('500.tpl');
                }
            });
            $app->get('/manage/locations', function() use ($smarty, $stock) {
                try {
                    $smarty->assign('locationsstockcount', $stock->getLocationStockCounts());
                    return $smarty->fetch('managelocations.tpl');
                } catch (Exception $e) {
                    $smarty->assign('error', $e->getMessage());
                    return $smarty->fetch('500.tpl');
                }
            });
            $app->post('/delete/location/{locationid}', function($locationid) use ($smarty, $stock, $app) {
                $locationid = filter_var($locationid, FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE);
                try {
                    $stock->deleteLocation($locationid);
                    return $app->redirect('/manage/locations');
                } catch (Exception $e) {
                    $smarty->assign('error', $e->getMessage());
                    return $smarty->fetch('500.tpl');
                }
            });
        }
    }
