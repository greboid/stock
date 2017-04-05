<?php
    declare(strict_types=1);

    require_once('../vendor/autoload.php');
    require_once('../configs/production.php');
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
    use \Aura\Auth\AuthFactory;
    use \Aura\Auth\Verifier\PasswordVerifier;
    use \Aura\Auth\Status;
    use \ICanBoogie\Storage\RunTimeStorage;
    use \Plasticbrain\FlashMessages\FlashMessages;


    use \Silex\Application;
    use \Symfony\Component\HttpFoundation\Request;

    $storage = new RunTimeStorage;
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
            $smarty->assign('error', $e->getMessage());
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
    $loginService = $auth_factory->newLoginService($pdo_adapter);
    $logoutService = $auth_factory->newLogoutService($pdo_adapter);
    $msg = new FlashMessages();
    $storage->store('flash', $msg);
    $pdo = $database->getPDO();


    $storage->store('auth', $auth);
    $storage->store('loginService', $loginService);
    $storage->store('logoutService', $logoutService);
    $storage->store('stock', $stock);
    $storage->store('smarty', $smarty);
    $storage->store('pdo', $database->getPDO());
    $storage->store('database', $database);



    $app = new Application();
    $storage->store('app', $app);
    if (DEBUG) {
        $app['debug'] = true;
    }
    $app->error(function (\Exception $e, Request $request, $code) use($smarty) {
        switch ($code) {
            case 404:
                return $smarty->fetch('404.tpl');
            default:
                $smarty->assign('error', 'An unknown error has occurred.');
                return $smarty->fetch('500.tpl');
        }
    });
    $app->before(function (Request $request, Application $app) use ($auth, $smarty, $stock, $msg) {
        $route = $request->attributes->get('_route');
        $smarty->assign('username', $auth->getUsername());
        $smarty->assign('sites', $stock->getSites());
        $smarty->assign('locations', $stock->getLocations());
        $smarty->assign('categories', $stock->getCategories());
        $smarty->assign('route', '/'.$route);
        if ($msg->hasMessages()) {
            $smarty->assign('msg', $msg->display(null, false));
        }
    });
    $systemRoutes->addRoutes($storage);
    $authRoutes->addRoutes($storage);
    $itemRoutes->addRoutes($storage);
    $locationRoutes->addRoutes($storage);
    $categoryRoutes->addRoutes($storage);
    $siteRoutes->addRoutes($storage);
    $userRoutes->addRoutes($storage);

    $app->run();
