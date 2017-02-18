<?php
    require './../vendor/autoload.php';
    require_once('../config.php');

    use greboid\stock\Stock;
    use \Bramus\Router\Router;

    $router = new Router();
    $stock = new Stock();
    $smarty = new Smarty();
    $smarty->setTemplateDir(TEMPLATES_PATH);
    $smarty->setCompileDir(TEMPLATES_CACHE_PATH);
    $smarty->setCacheDir(CACHE_PATH);
    $smarty->setConfigDir(CONFIG_PATH);
    $smarty->assign('max_stock', MAX_STOCK);
    $error = false;

    $router->set404(function() use ($smarty) {
        header('HTTP/1.1 404 Not Found');
        $smarty->display('404.tpl');
    });
    $router->get('/', function() use($smarty, $stock) {
        try {
            $smarty->assign('sites', $stock->getSites());
            $smarty->assign('locations', $stock->getLocations());
            $smarty->display('index.tpl');
        } catch (Exception $e) {
            $smarty->assign('error', $e->getMessage());
            $smarty->display('500.tpl');
        }
    });
    $router->get('/site/(\d+)', function($siteid) use ($smarty, $stock) {
        $siteid = filter_var($siteid, FILTER_UNSAFE_RAW);
        try {
            if ($stock->getSiteName($siteid) !== FALSE) {
                $smarty->assign('sites', $stock->getSites());
                $smarty->assign('locations', $stock->getLocations());
                $smarty->assign('siteid', $siteid);
                $smarty->assign('site', $stock->getSiteName($siteid));
                $smarty->assign('stock', $stock->getStock($siteid));
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
    $router->get('/site/(.*)', function($siteName) use ($smarty, $stock) {
        $siteName = filter_var($siteName, FILTER_UNSAFE_RAW);
        $siteid = $stock->getSiteID($siteName);
        if ($siteid === FALSE) {
            header('HTTP/1.1 404 Not Found');
            $smarty->display('404.tpl');
        }
        try {
            if ($stock->getSiteName($siteid) !== FALSE) {
                $smarty->assign('sites', $stock->getSites());
                $smarty->assign('locations', $stock->getLocations());
                $smarty->assign('siteid', $siteid);
                $smarty->assign('site', $stock->getSiteName($siteid));
                $smarty->assign('stock', $stock->getStock($siteid));
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
    $router->get('/add/item', function() use ($smarty, $stock) {
        try {
            $smarty->assign('sites', $stock->getSites());
            $smarty->assign('locations', $stock->getLocations());
            $smarty->display('additem.tpl');
        } catch (Exception $e) {
            $smarty->assign('error', $e->getMessage());
            $smarty->display('500.tpl');
        }
    });
    $router->post('/add/item', function() use ($smarty, $stock) {
        try {
            if (isset($_POST['name']) && isset($_POST['location']) && isset($_POST['count'])) {
                $name = filter_var($_POST['name'], FILTER_UNSAFE_RAW);
                $location = filter_var($_POST['location'], FILTER_UNSAFE_RAW);
                $count = filter_var($_POST['count'], FILTER_UNSAFE_RAW);
                $stock->insertItem($name, $location, $count);
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
            if (isset($_POST['name'])) {
                $name = filter_var($_POST['name'], FILTER_UNSAFE_RAW);
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
    $router->get('/add/location', function() use ($smarty, $stock) {
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
    $router->post('/site/(\d+)', function($siteid) use ($smarty, $stock) {
        try {
            $itemid = filter_var($_POST['itemid'], FILTER_UNSAFE_RAW);
            $count = filter_var($_POST['count'], FILTER_UNSAFE_RAW);
            if (isset($_POST['itemid']) && isset($_POST['count'])) {
                $stock->editItem($itemid, $count);
            } else {
                $smarty->assign('error', 'Missing required value.');
                $smarty->display('500.tpl');
            }
            header('Location: /site/'.$siteid);
        } catch (Exception $e) {
            $smarty->assign('error', $e->getMessage());
            $smarty->display('500.tpl');
        }
    });
    $router->run();
