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
        try {
            $smarty->assign('sites', $stock->getSites());
            $smarty->assign('locations', $stock->getLocations());
            $smarty->assign('siteid', $siteid);
            $smarty->assign('site', $stock->getSiteName($siteid));
            $smarty->assign('stock', $stock->getStock($siteid));
            $smarty->display('stock.tpl');
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
                $stock->insertItem($_POST['name'], $_POST['location'], $_POST['count']);
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
                $stock->insertSite($_POST['name']);
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
                $stock->insertLocation($_POST['name'], $_POST['site']);
            }
            header('Location: /');
        } catch (Exception $e) {
            $smarty->assign('error', $e->getMessage());
            $smarty->display('500.tpl');
        }
    });
    $router->post('/site/(\d+)', function($siteid) use ($smarty, $stock) {
        try {
            if (isset($_POST['itemid']) && isset($_POST['count'])) {
                $stock->editItem($_POST['itemid'], $_POST['count']);
            }
            header('Location: /site/'.$siteid);
        } catch (Exception $e) {
            $smarty->assign('error', $e->getMessage());
            $smarty->display('500.tpl');
        }
    });
    $router->run();
