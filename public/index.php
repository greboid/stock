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
    use \Aura\Auth\Verifier\PasswordVerifier;
    use ICanBoogie\Storage\RunTimeStorage;

    $storage = new RunTimeStorage;
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
    $auth_factory = new AuthFactory($_COOKIE);
    $auth = $auth_factory->newInstance();
    $pdo = new \PDO('mysql:dbname='.STOCK_DB.';host='.STOCK_DB_HOST, STOCK_DB_USER, STOCK_DB_PW);
    $hash = new PasswordVerifier(PASSWORD_DEFAULT);
    $cols = array('username', 'password', 'email', 'name', 'id');
    $from = 'accounts';
    $where = 'active = 1 AND verified = 1';
    $pdo_adapter = $auth_factory->newPdoAdapter($pdo, $hash, $cols, $from, $where);
    $login_service = $auth_factory->newLoginService($pdo_adapter);
    $logout_service = $auth_factory->newLogoutService($pdo_adapter);
    $resume_service = $auth_factory->newResumeService($pdo_adapter);
    $resume_service->resume($auth);

    try {
        $stock = new Stock();
    } catch (Exception $e) {
        $smarty->assign('error', 'The database connection settings are wrong, please check the config');
        $smarty->display('500.tpl');
    }


    $storage->store('auth', $auth);
    $storage->store('loginService', $login_service);
    $storage->store('logoutService', $logout_service);
    $storage->store('resumeService', $resume_service);
    $storage->store('stock', $stock);
    $storage->store('smarty', $smarty);

    $authRoutes->addRoutes($router, $storage);
    $router->before('GET', '(.*)', function($route) use ($smarty, $stock, $auth) {
        $smarty->assign('sites', $stock->getSites());
        $smarty->assign('locations', $stock->getLocations());
        $smarty->assign('categories', $stock->getCategories());
    });
    $systemRoutes->addRoutes($router, $smarty, $stock, $storage);
    $itemRoutes->addRoutes($router, $smarty, $stock, $storage);
    $locationRoutes->addRoutes($router, $smarty, $stock, $storage);
    $categoryRoutes->addRoutes($router, $smarty, $stock, $storage);
    $siteRoutes->addRoutes($router, $smarty, $stock, $storage);

    $router->run();
