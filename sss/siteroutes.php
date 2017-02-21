<?php

    namespace greboid\stock;

    use \Exception;

    class SiteRoutes {

        public function addRoutes($router, $smarty, $stock) {
            $router->get('/site/(.*)', function($siteName) use ($smarty, $stock) {
                $siteName = filter_var($siteName, FILTER_UNSAFE_RAW);
                $siteid = $stock->getSiteID($siteName);
                if ($siteid === false) {
                    header('HTTP/1.1 404 Not Found');
                    $smarty->display('404.tpl');
                }
                try {
                    if ($stock->getSiteName($siteid) !== false) {
                        $smarty->assign('sites', $stock->getSites());
                        $smarty->assign('locations', $stock->getLocations());
                        $smarty->assign('siteid', $siteid);
                        $smarty->assign('site', $stock->getSiteName($siteid));
                        $smarty->assign('stock', $stock->getSiteStock($siteid));
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
            $router->get('/add/site', function() use ($smarty, $stock) {
                try {
                    $smarty->assign('sites', $stock->getSites());
                    $smarty->assign('locations', $stock->getLocations());
                    $smarty->display('addsite.tpl');
                } catch (Exception $e) {
                    $smarty->assign('error', $e->getMessage());
                    $smarty->display('500.tpl');
                }
            });
            $router->post('/add/site', function() use ($smarty, $stock) {
                try {
                    $name = filter_input(INPUT_POST, "name", FILTER_UNSAFE_RAW, FILTER_null_ON_FAILURE);
                    if ($name !== false) {
                        $stock->insertSite($name);
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
            $router->get('/manage/sites', function() use ($smarty, $stock) {
                try {
                    $smarty->assign('sites', $stock->getSites());
                    $smarty->assign('locations', $stock->getLocations());
                    $smarty->display('managesites.tpl');
                } catch (Exception $e) {
                    $smarty->assign('error', $e->getMessage());
                    $smarty->display('500.tpl');
                }
            });
            $router->post('/delete/site/(\d+)', function($siteid) use ($smarty, $stock) {
                try {
                    $stock->deleteSite($siteid);
                    header('Location: /manage/sites');
                } catch (Exception $e) {
                    $smarty->assign('error', $e->getMessage());
                    $smarty->display('500.tpl');
                }
            });
        }
    }
