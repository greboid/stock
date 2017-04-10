<?php
    declare(strict_types=1);

    namespace greboid\stock;

    use \Exception;
    use \greboid\stock\Stock;
    use \Silex\Application;

    class SiteRoutes {

        public function addRoutes(Application $app): void {

            $app->get('/site/{siteName}', function(Application $app, $siteName) {
                $siteName = filter_var($siteName, FILTER_UNSAFE_RAW);
                $siteid = $app['stock']->getSiteID($siteName);
                if ($siteid === false) {
                    return $app->abort(404, 'Site '.$siteid.' not found');
                }
                try {
                    if ($app['stock']->getSiteName($siteid) !== false) {
                        return $app['twig']->render('stock.tpl', array(
                            'siteid' => $siteid,
                            'categories' => $app['stock']->getCategories(),
                            'site' => $app['stock']->getSiteName($siteid),
                            'stock' => $app['stock']->getSiteStock($siteid),
                        ));
                    } else {
                        return $app->abort(404, 'Site '.$siteid.' not found');
                    }
                } catch (Exception $e) {
                    return $app->abort(500, $e->getMessage());
                }
            });
            $app->get('/json/site/{siteName}', function(Application $app, $siteName) {
                $siteName = filter_var($siteName, FILTER_UNSAFE_RAW);
                $siteid = $app['stock']->getSiteID($siteName);
                if ($siteid === false) {
                    return $app->abort(404, 'Site '.$siteid.' not found');
                }
                try {
                    if ($app['stock']->getSiteName($siteid) !== false) {
                        return $app['twig']->render('outputjson.tpl', array(
                            'output' => $app['stock']->getSiteStock($siteid),
                        ));
                    } else {
                        return $app->abort(500, 'Missing required value.');
                    }
                } catch (Exception $e) {
                    return $app->abort(500, $e->getMessage());
                }
            });
            $app->get('/add/site', function(Application $app) {
                try {
                    return $app['twig']->render('addsite.tpl', array());
                } catch (Exception $e) {
                    return $app->abort(500, $e->getMessage());
                }
            });
            $app->post('/edit/site', function(Application $app) {
                try {
                    $siteID = filter_input(INPUT_POST, "editID", FILTER_VALIDATE_INT);
                    $name = filter_input(INPUT_POST, "editName", FILTER_UNSAFE_RAW, FILTER_NULL_ON_FAILURE);
                    if ($name !== false) {
                        $app['stock']->editSite($siteID, $name);
                    } else {
                        return $app->abort(500, 'Missing required value.');
                    }
                    return $app->redirect('/manage/sites');
                } catch (Exception $e) {
                    return $app->abort(500, $e->getMessage());
                }
            });
            $app->post('/add/site', function(Application $app) {
                try {
                    $name = filter_input(INPUT_POST, "addName", FILTER_UNSAFE_RAW, FILTER_NULL_ON_FAILURE);
                    if ($name !== false) {
                        $app['stock']->insertSite($name);
                    } else {
                        return $app->abort(500, 'Missing required value.');
                    }
                    return $app->redirect('/manage/sites');
                } catch (Exception $e) {
                    return $app->abort(500, $e->getMessage());
                }
            });
            $app->get('/manage/sites', function(Application $app) {
                try {
                    return $app['twig']->render('managesites.tpl', array());
                } catch (Exception $e) {
                    return $app->abort(500, $e->getMessage());
                }
            });
            $app->post('/delete/site/{siteid}', function(Application $app, $siteid) {
                $siteid = filter_var($siteid, FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE);
                try {
                    $app['stock']->deleteSite($siteid);
                    return $app->redirect('/manage/sites');
                } catch (Exception $e) {
                    return $app->abort(500, $e->getMessage());
                }
            });
        }
    }
