<?php
    declare(strict_types=1);

    namespace greboid\stock;

    use \Exception;
    use \greboid\stock\Stock;
    use \Bramus\Router\Router;
    use \Smarty;
    use \ICanBoogie\Storage\RunTimeStorage;

    class SiteRoutes {

        public function addRoutes(RunTimeStorage $storage): void {
            $app = $storage->retrieve('app');
            $smarty = $storage->retrieve('smarty');
            $stock = $storage->retrieve('stock');

            $app->get('/site/{siteName}', function($siteName) use ($smarty, $stock) {
                $siteName = filter_var($siteName, FILTER_UNSAFE_RAW);
                $siteid = $stock->getSiteID($siteName);
                if ($siteid === false) {
                    header('HTTP/1.1 404 Not Found');
                    return $smarty->fetch('404.tpl');
                }
                try {
                    if ($stock->getSiteName($siteid) !== false) {
                        $smarty->assign('siteid', $siteid);
                        $smarty->assign('categories', $stock->getCategories());
                        $smarty->assign('site', $stock->getSiteName($siteid));
                        $smarty->assign('stock', $stock->getSiteStock($siteid));
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
            $app->get('/json/site/{siteName}', function($siteName) use ($smarty, $stock) {
                $siteName = filter_var($siteName, FILTER_UNSAFE_RAW);
                $siteid = $stock->getSiteID($siteName);
                if ($siteid === false) {
                    header('HTTP/1.1 404 Not Found');
                }
                try {
                    if ($stock->getSiteName($siteid) !== false) {
                        $smarty->assign('output', $stock->getSiteStock($siteid));
                        return $smarty->fetch('outputjson.tpl');
                    } else {
                        header('HTTP/1.1 404 Not Found');
                    }
                } catch (Exception $e) {
                    header('HTTP/1.1 500 Oops');
                }
            });
            $app->get('/add/site', function() use ($smarty, $stock) {
                try {
                    return $smarty->fetch('addsite.tpl');
                } catch (Exception $e) {
                    $smarty->assign('error', $e->getMessage());
                    return $smarty->fetch('500.tpl');
                }
            });
            $app->post('/edit/site', function() use ($smarty, $stock, $app) {
                try {
                    $siteID = filter_input(INPUT_POST, "editID", FILTER_VALIDATE_INT);
                    $name = filter_input(INPUT_POST, "editName", FILTER_UNSAFE_RAW, FILTER_NULL_ON_FAILURE);
                    if ($name !== false) {
                        $stock->editSite($siteID, $name);
                    } else {
                        $smarty->assign('error', 'Missing required value.');
                        return $smarty->fetch('500.tpl');
                    }
                    return $app->redirect('/manage/sites');
                } catch (Exception $e) {
                    $smarty->assign('error', $e->getMessage());
                    return $smarty->fetch('500.tpl');
                }
            });
            $app->post('/add/site', function() use ($smarty, $stock, $app) {
                try {
                    $name = filter_input(INPUT_POST, "addName", FILTER_UNSAFE_RAW, FILTER_NULL_ON_FAILURE);
                    if ($name !== false) {
                        $stock->insertSite($name);
                    } else {
                        $smarty->assign('error', 'Missing required value.');
                        return $smarty->fetch('500.tpl');
                    }
                    return $app->redirect('/manage/sites');
                } catch (Exception $e) {
                    $smarty->assign('error', $e->getMessage());
                    return $smarty->fetch('500.tpl');
                }
            });
            $app->get('/manage/sites', function() use ($smarty, $stock) {
                try {
                    return $smarty->fetch('managesites.tpl');
                } catch (Exception $e) {
                    $smarty->assign('error', $e->getMessage());
                    return $smarty->fetch('500.tpl');
                }
            });
            $app->post('/delete/site/{siteid}', function($siteid) use ($smarty, $stock, $app) {
                $siteid = filter_var($siteid, FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE);
                try {
                    $stock->deleteSite($siteid);
                    return $app->redirect('/manage/sites');
                } catch (Exception $e) {
                    $smarty->assign('error', $e->getMessage());
                    return $smarty->fetch('500.tpl');
                }
            });
        }
    }
