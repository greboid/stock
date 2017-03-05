<?php
    declare(strict_types=1);

    require './../vendor/autoload.php';
    require_once('../config.php');

    use \greboid\stock\Stock;
    use \greboid\stock\ItemRoutes;
    use \greboid\stock\LocationRoutes;
    use \greboid\stock\CategoryRoutes;
    use \greboid\stock\SiteRoutes;
    use \Bramus\Router\Router;

    $router = new Router();
    $smarty = new Smarty();
    $itemRoutes = new ItemRoutes();
    $locationRoutes = new LocationRoutes();
    $categoryRoutes = new CategoryRoutes();
    $siteRoutes = new SiteRoutes();
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
        $smarty->assign('sites', $stock->getSites());
        $smarty->assign('locations', $stock->getLocations());
        $smarty->assign('categories', $stock->getCategories());
        $version = $stock->checkVersion();
        if (preg_match('#^(?!setup).*#', $route) && !$version) {
            $smarty->assign('version', $version);
            $smarty->display('install.tpl');
            exit();
        }
    });
    $router->set404(function() use ($smarty, $stock) {
        header('HTTP/1.1 404 Not Found');
        $smarty->display('404.tpl');
    });
    $router->get('/setup/dropandcreate', function() use ($smarty, $stock) {
        $stock->dropAndCreate();
        header('Location: /');
    });
    $router->get('/setup/dbupgrade', function() use ($smarty, $stock) {
        $stock->upgrade();
        header('Location: /');
    });
    $router->get('/', function() use($smarty, $stock) {
        try {
            $smarty->display('index.tpl');
        } catch (Exception $e) {
            $smarty->assign('error', $e->getMessage());
            $smarty->display('500.tpl');
        }
    });

    $itemRoutes->addRoutes($router, $smarty, $stock);
    $locationRoutes->addRoutes($router, $smarty, $stock);
    $categoryRoutes->addRoutes($router, $smarty, $stock);
    $siteRoutes->addRoutes($router, $smarty, $stock);

    $router->run();
