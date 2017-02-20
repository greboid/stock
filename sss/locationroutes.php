<?php

    namespace greboid\stock;

    use \Exception;

    class LocationRoutes {

        function addRoutes($router, $smarty, $stock) {

            $router->get('/location/(.*)', function($locationName) use ($smarty, $stock) {
                $locationName = filter_var($locationName, FILTER_UNSAFE_RAW);
                $locationid = $stock->getLocationID($locationName);
                if ($locationid === FALSE) {
                    header('HTTP/1.1 404 Not Found');
                    $smarty->display('404.tpl');
                }
                try {
                    if ($stock->getLocationName($locationid) !== FALSE) {
                        $smarty->assign('sites', $stock->getSites());
                        $smarty->assign('locations', $stock->getLocations());
                        $smarty->assign('locationid', $locationid);
                        $smarty->assign('site', $stock->getLocationName($locationid));
                        $smarty->assign('stock', $stock->getLocationStock($locationid));
                        $smarty->display('stock.tpl');
                    } else {
                        header('HTTP/1.1 404 Not Found');
                        $smarty->display('404.tpl');
                    }
                } catch (Exception $e) {
                    $smarty->assign('error', $e->getMessage());
                    $smarty->display('500.tpl');
                }
            });
            $router->get('/add/location', function() use ($smarty, $stock) {
                if (count($stock->getLocations()) == 0) {
                    header('Location: /add/site');
                }
                try {
                    $smarty->assign('sites', $stock->getSites());
                    $smarty->assign('locations', $stock->getLocations());
                    $smarty->display('addlocation.tpl');
                } catch (Exception $e) {
                    $smarty->assign('error', $e->getMessage());
                    $smarty->display('500.tpl');
                }
            });
            $router->post('/add/location', function() use ($smarty, $stock) {
                try {
                    if (isset($_POST['name']) && isset($_POST['site'])) {
                        $name = filter_var($_POST['name'], FILTER_UNSAFE_RAW);
                        $site = filter_var($_POST['site'], FILTER_UNSAFE_RAW);
                        $stock->insertLocation($name, $site);
                    } else {
                        $smarty->assign('error', 'Missing required value.');
                        $smarty->display('500.tpl');
                    }
                    header('Location: /');
                } catch (Exception $e) {
                    $smarty->assign('error', $e->getMessage());
                    $smarty->display('500.tpl');
                }
            });
            $router->get('/manage/locations', function() use ($smarty, $stock) {
                try {
                    $smarty->assign('sites', $stock->getSites());
                    $smarty->assign('locations', $stock->getLocations());
                    $smarty->assign('locationsstockcount', $stock->getLocationStockCounts());
                    $smarty->display('managelocations.tpl');
                } catch (Exception $e) {
                    $smarty->assign('error', $e->getMessage());
                    $smarty->display('500.tpl');
                }
            });
            $router->post('/delete/location/(\d+)', function($locationid) use ($smarty, $stock) {
                try {
                    $stock->deleteLocation($locationid);
                    header('Location: /manage/locations');
                } catch (Exception $e) {
                    $smarty->assign('error', $e->getMessage());
                    $smarty->display('500.tpl');
                }
            });
        }
    }