<?php
    declare(strict_types=1);

    namespace greboid\stock;

    use \Exception;
    use \greboid\stock\Stock;
    use \Bramus\Router\Router;
    use \Smarty;

    class SiteRoutes {

        public function addRoutes(Router $router, Smarty $smarty, Stock $stock): void {
            $router->get('/site/(.*)', function($siteName) use ($smarty, $stock) {
                $siteName = filter_var($siteName, FILTER_UNSAFE_RAW);
                $siteid = $stock->getSiteID($siteName);
                if ($siteid === false) {
                    header('HTTP/1.1 404 Not Found');
                    $smarty->display('404.tpl');
                }
                try {
                    if ($stock->getSiteName($siteid) !== false) {
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
            $router->get('/json/site/(.*)', function($siteName) use ($smarty, $stock) {
                $siteName = filter_var($siteName, FILTER_UNSAFE_RAW);
                $siteid = $stock->getSiteID($siteName);
                if ($siteid === false) {
                    header('HTTP/1.1 404 Not Found');
                }
                try {
                    if ($stock->getSiteName($siteid) !== false) {
                        $smarty->assign('output', $stock->getSiteStock($siteid));
                        $smarty->display('outputjson.tpl');
                    } else {
                        header('HTTP/1.1 404 Not Found');
                    }
                } catch (Exception $e) {
                    header('HTTP/1.1 500 Oops');
                }
            });
            $router->get('/add/site', function() use ($smarty, $stock) {
                try {
                    $smarty->display('addsite.tpl');
                } catch (Exception $e) {
                    $smarty->assign('error', $e->getMessage());
                    $smarty->display('500.tpl');
                }
            });
            $router->post('/edit/site', function() use ($smarty, $stock) {
                try {
                    $siteID = filter_input(INPUT_POST, "editID", FILTER_VALIDATE_INT);
                    $name = filter_input(INPUT_POST, "editName", FILTER_UNSAFE_RAW, FILTER_NULL_ON_FAILURE);
                    if ($name !== false) {
                        $stock->editSite($siteID, $name);
                    } else {
                        $smarty->assign('error', 'Missing required value.');
                        $smarty->display('500.tpl');
                    }
                    header('Location: /manage/sites');
                } catch (Exception $e) {
                    $smarty->assign('error', $e->getMessage());
                    $smarty->display('500.tpl');
                }
            });
            $router->post('/add/site', function() use ($smarty, $stock) {
                try {
                    $name = filter_input(INPUT_POST, "addName", FILTER_UNSAFE_RAW, FILTER_NULL_ON_FAILURE);
                    if ($name !== false) {
                        $stock->insertSite($name);
                    } else {
                        $smarty->assign('error', 'Missing required value.');
                        $smarty->display('500.tpl');
                    }
                    header('Location: /manage/sites');
                } catch (Exception $e) {
                    $smarty->assign('error', $e->getMessage());
                    $smarty->display('500.tpl');
                }
            });
            $router->get('/manage/sites', function() use ($smarty, $stock) {
                try {
                    $smarty->display('managesites.tpl');
                } catch (Exception $e) {
                    $smarty->assign('error', $e->getMessage());
                    $smarty->display('500.tpl');
                }
            });
            $router->post('/delete/site/(\d+)', function($siteid) use ($smarty, $stock) {
                $siteid = filter_var($siteid, FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE);
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
