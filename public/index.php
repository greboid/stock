<?php
    require './../vendor/autoload.php';
    require_once('../config.php');

    use greboid\stock\Stock;
    use \Bramus\Router\Router;

    $router = new Router();
    $smarty = new Smarty();
    $smarty->setTemplateDir(TEMPLATES_PATH);
    $smarty->setCompileDir(TEMPLATES_CACHE_PATH);
    $smarty->setCacheDir(CACHE_PATH);
    $smarty->setConfigDir(CONFIG_PATH);
    $smarty->assign('max_stock', MAX_STOCK);
    $error = false;

    try {
        $stock = new Stock();
    } catch (Exception $e) {
        $smarty->assign('error', 'The database connection settings are wrong, please check the config');
        $smarty->display('500.tpl');
    }

    //The regex should be matched here, but I can't make the router like it... hack it
    $router->before('GET', '(.*)', function($route) use ($smarty, $stock) {
        $version = $stock->checkVersion();
        if (preg_match('#^(?!setup).*#', $route) && !$version) {
            $smarty->assign('version', $version);
            $smarty->display('install.tpl');
            exit();
        }
    });
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
    $router->get('/add/item', function() use ($smarty, $stock) {
        if (count($stock->getSites()) == 0) {
            header('Location: /add/location');
        }
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
    $router->get('/manage/items', function() use ($smarty, $stock) {
        try {
            $smarty->assign('sites', $stock->getSites());
            $smarty->assign('locations', $stock->getLocations());
            $smarty->assign('stock', $stock->getSiteStock(0));
            $smarty->display('manageitems.tpl');
        } catch (Exception $e) {
            $smarty->assign('error', $e->getMessage());
            $smarty->display('500.tpl');
        }
    });
    $router->post('/delete/item/(\d+)', function($itemid) use ($smarty, $stock) {
        try {
            $stock->deleteItem($itemid);
            header('Location: /manage/items');
        } catch (Exception $e) {
            $smarty->assign('error', $e->getMessage());
            $smarty->display('500.tpl');
        }
    });
    $router->run();
