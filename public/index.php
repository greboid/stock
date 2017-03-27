<?php
    declare(strict_types=1);

    require './../vendor/autoload.php';
    require_once('../config.php');
    session_start();

    use \greboid\stock\Database;
    use \greboid\stock\Stock;
    use \greboid\stock\ItemRoutes;
    use \greboid\stock\LocationRoutes;
    use \greboid\stock\CategoryRoutes;
    use \greboid\stock\SiteRoutes;
    use \greboid\stock\SystemRoutes;
    use \greboid\stock\AuthRoutes;
    use \greboid\stock\UserRoutes;
    use \Bramus\Router\Router;
    use \Aura\Auth\AuthFactory;
    use \Aura\Auth\Verifier\PasswordVerifier;
    use \Aura\Auth\Status;
    use \ICanBoogie\Storage\RunTimeStorage;
    use \Plasticbrain\FlashMessages\FlashMessages;

    $storage = new RunTimeStorage;
    $router = new Router();
    $smarty = new Smarty();
    $itemRoutes = new ItemRoutes();
    $locationRoutes = new LocationRoutes();
    $categoryRoutes = new CategoryRoutes();
    $siteRoutes = new SiteRoutes();
    $systemRoutes = new SystemRoutes();
    $authRoutes = new AuthRoutes();
    $userRoutes = new UserRoutes();
    $smarty->setTemplateDir(TEMPLATES_PATH);
    $smarty->setCompileDir(TEMPLATES_CACHE_PATH);
    $smarty->setCacheDir(CACHE_PATH);
    $smarty->setConfigDir(CONFIG_PATH);
    $smarty->assign('max_stock', MAX_STOCK);
    $error = false;
    $database = null;

    try {
        $database = new Database();
    } catch (Exception $e) {
        if ($e->getMessage() == 'Unable to connect to the database.') {
            $smarty->assign('error', 'The database connection settings are wrong, please check the config');
            $smarty->display('500.tpl');
        } else {
            $smarty->assign('version', false);
            $smarty->display('install.tpl');
        }
        exit();
    }
    $stock = new Stock($database);
    $auth_factory = new AuthFactory($_COOKIE);
    $auth = $auth_factory->newInstance();
    $hash = new PasswordVerifier(PASSWORD_DEFAULT);
    $cols = array('username', 'password', 'email', 'name', 'id');
    $from = 'accounts';
    $where = 'active = 1 AND verified = 1';
    $pdo_adapter = $auth_factory->newPdoAdapter($database->getPDO(), $hash, $cols, $from, $where);
    $login_service = $auth_factory->newLoginService($pdo_adapter);
    $logout_service = $auth_factory->newLogoutService($pdo_adapter);
    $msg = new FlashMessages();
    $storage->store('flash', $msg);

    if ($auth->getStatus() !== Status::VALID) {
        $smarty->assign('loginMessage', LOGIN_MESSAGE);
        $smarty->display('login.tpl');
    }


    $storage->store('auth', $auth);
    $storage->store('loginService', $login_service);
    $storage->store('logoutService', $logout_service);
    $storage->store('stock', $stock);
    $storage->store('smarty', $smarty);
    $storage->store('router', $router);
    $storage->store('pdo', $database->getPDO());
    $storage->store('database', $database);

    $authRoutes->addRoutes($router, $storage);
    if ($auth->getStatus() === Status::VALID) {
        $systemRoutes->addRoutes($router, $smarty, $storage);
        $router->before('GET', '(.*)', function($route) use ($smarty, $stock, $auth, $msg) {
            $smarty->assign('username', $auth->getUsername());
            $smarty->assign('sites', $stock->getSites());
            $smarty->assign('locations', $stock->getLocations());
            $smarty->assign('categories', $stock->getCategories());
            $smarty->assign('route', '/'.$route);
            if ($msg->hasMessages()) {
                $smarty->assign('msg', $msg->display(null, false));
            }
        });
        $itemRoutes->addRoutes($router, $smarty, $stock, $storage);
        $locationRoutes->addRoutes($router, $smarty, $stock, $storage);
        $categoryRoutes->addRoutes($router, $smarty, $stock, $storage);
        $siteRoutes->addRoutes($router, $smarty, $stock, $storage);
        $userRoutes->addRoutes($storage);
    }

    $router->run();
