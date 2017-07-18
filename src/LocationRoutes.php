<?php
    declare(strict_types=1);

    namespace greboid\stock;

    use \Exception;
    use \greboid\stock\Stock;
    use \Silex\Application;

    class LocationRoutes {

        public function addRoutes(Application $app): void {

            $app->get('/location/', function(Application $app) {
                return $app['twig']->render('locations.tpl', array());
            });
            $app->get('/location/manage', function(Application $app) {
                try {
                    return $app['twig']->render('managelocations.tpl', array(
                        'locationsstockcount' => $app['stock']->getLocationStockCounts(),
                    ));
                } catch (Exception $e) {
                    return $app->abort(500, $e->getMessage());
                }
            });
            $app->get('/location/{locationName}', function(Application $app, $locationName) {
                $locationName = filter_var($locationName, FILTER_UNSAFE_RAW);
                $locationid = $app['stock']->getLocationID($locationName);
                if ($locationid === false) {
                    return $app->abort(404, 'Location '.$locationid.' not found.');
                }
                try {
                    if ($app['stock']->getLocationName($locationid) !== false) {
                        return $app['twig']->render('stock.tpl', array(
                            'locationid' => $locationid,
                            'site' => $app['stock']->getLocationName($locationid),
                            'stock' => $app['stock']->getLocationStock($locationid),
                        ));
                    } else {
                        return $app->abort(404, 'Location '.$locationid.' not found.');
                    }
                } catch (Exception $e) {
                    return $app->abort(500, $e->getMessage());
                }
            });
            $app->get('/location/add', function(Application $app) {
                if (count($app['stock']->getLocations()) == 0) {
                    return $app->redirect('/site/add');
                }
                try {
                    return $app['twig']->render('addlocation.tpl', array());
                } catch (Exception $e) {
                    return $app->abort(500, $e->getMessage());
                }
            });
            $app->post('/location/add', function(Application $app) {
                try {
                    $name = filter_input(INPUT_POST, "name", FILTER_UNSAFE_RAW, FILTER_NULL_ON_FAILURE);
                    $site = filter_input(INPUT_POST, "site", FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE);
                    if ($name !== false && $site !== false) {
                        $app['stock']->insertLocation($name, $site);
                    } else {
                        return $app->abort(500, 'Missing required value.');
                    }
                    return $app->redirect('/location/manage');
                } catch (Exception $e) {
                    return $app->abort(500, $e->getMessage());
                }
            });
            $app->post('/location/edit', function(Application $app) {
                try {
                    $locationID = filter_input(INPUT_POST, "editID", FILTER_VALIDATE_INT);
                    $locationName = filter_input(INPUT_POST, "editName", FILTER_UNSAFE_RAW, FILTER_NULL_ON_FAILURE);
                    $siteID = filter_input(INPUT_POST, "editSite", FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE);
                    if ($locationName !== false) {
                        $app['stock']->editLocation($locationID, $locationName, $siteID);
                    } else {
                        return $app->abort(500, 'Missing required value.');
                    }
                    return $app->redirect('/location/manage');
                } catch (Exception $e) {
                    return $app->abort(500, $e->getMessage());
                }
            });
            $app->post('/location/delete/{locationid}', function(Application $app, $locationid) {
                $locationid = filter_var($locationid, FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE);
                try {
                    $app['stock']->deleteLocation($locationid);
                    return $app->redirect('/location/manage');
                } catch (Exception $e) {
                    return $app->abort(500, $e->getMessage());
                }
            });
        }
    }
