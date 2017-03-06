<?php
    declare(strict_types=1);

    require './../vendor/autoload.php';
    require_once('../config.php');

    use \greboid\stock\Stock;
    use \greboid\stock\ItemRoutes;
    use \greboid\stock\LocationRoutes;
    use \greboid\stock\CategoryRoutes;
    use \greboid\stock\SiteRoutes;
    use \greboid\stock\SystemRoutes;
    use \greboid\stock\AuthRoutes;
    use \Bramus\Router\Router;
    use \Aura\Auth\AuthFactory;

    $router = new Router();
    $smarty = new Smarty();
    $itemRoutes = new ItemRoutes();
    $locationRoutes = new LocationRoutes();
    $categoryRoutes = new CategoryRoutes();
    $siteRoutes = new SiteRoutes();
    $systemRoutes = new SystemRoutes();
    $authRoutes = new AuthRoutes();
    $smarty->setTemplateDir(TEMPLATES_PATH);
    $smarty->setCompileDir(TEMPLATES_CACHE_PATH);
    $smarty->setCacheDir(CACHE_PATH);
    $smarty->setConfigDir(CONFIG_PATH);
    $smarty->assign('max_stock', MAX_STOCK);
    $error = false;
    $auth = (new AuthFactory($_COOKIE))->newInstance();

    try {
        $stock = new Stock();
    } catch (Exception $e) {
        $smarty->assign('error', 'The database connection settings are wrong, please check the config');
        $smarty->display('500.tpl');
    }

    $authRoutes->addRoutes($router, $smarty, $stock, $auth);
    $router->before('GET', '(.*)', function($route) use ($smarty, $stock, $auth) {
        $smarty->assign('sites', $stock->getSites());
        $smarty->assign('locations', $stock->getLocations());
        $smarty->assign('categories', $stock->getCategories());
    });
    $systemRoutes->addRoutes($router, $smarty, $stock);
    $itemRoutes->addRoutes($router, $smarty, $stock);
    $locationRoutes->addRoutes($router, $smarty, $stock);
    $categoryRoutes->addRoutes($router, $smarty, $stock);
    $siteRoutes->addRoutes($router, $smarty, $stock);

    $router->run();
