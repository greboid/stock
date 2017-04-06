<?php
    declare(strict_types=1);

    require_once('../vendor/autoload.php');
    require_once('../configs/production.php');

    use \greboid\stock\Database;
    use \greboid\stock\Stock;
    use \greboid\stock\ItemRoutes;
    use \greboid\stock\LocationRoutes;
    use \greboid\stock\CategoryRoutes;
    use \greboid\stock\SiteRoutes;
    use \greboid\stock\SystemRoutes;
    use \greboid\stock\AuthRoutes;
    use \greboid\stock\UserRoutes;
    use \greboid\stock\UserProvider;
    use \ICanBoogie\Storage\RunTimeStorage;


    use \Silex\Application;
    use \Symfony\Component\HttpFoundation\Request;
    use \Symfony\Component\HttpFoundation\Session\Session;
    use \Silex\Provider\SessionServiceProvider;
    use \Silex\Provider\SecurityServiceProvider;

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


    $storage->store('stock', $stock);
    $storage->store('smarty', $smarty);
    $storage->store('pdo', $database->getPDO());
    $storage->store('database', $database);


    $session = new Session();
    $app = new Application();
    $storage->store('app', $app);
    $app['db'] = $database->getPDO();
    $app['session'] = $session;
    if (DEBUG) {
        $app['debug'] = true;
    }
    $app->register(new SessionServiceProvider());
    $app->register(new SecurityServiceProvider(), array(
        'security.firewalls' => array(
            'login' => array(
                'pattern' => '^/auth/login$',
                'anonymous' => true,
            ),
            'general' => array(
                'pattern' => '^.*$',
                'anonymous' => false,
                'form' => array(
                    'login_path' => '/auth/login',
                    'check_path' => '/auth/login_check',
                    'default_target_path' => '/',
                ),
                'logout' => array(
                    'logout_path' => '/auth/logout',
                    'target_url' => '/auth/loggedout'
                ),
                'users' => function () use ($app) {
                    return new UserProvider($app['db']);
                },
            )
        ),
    ));


    $app->error(function (\Exception $e, Request $request, $code) use($smarty, $app) {
        switch ($code) {
            case 404:
                return $smarty->fetch('404.tpl');
            default:
                $smarty->assign('error', 'An unknown error has occurred.');
                return $smarty->fetch('500.tpl');
        }
    });
    $app->before(function (Request $request, Application $app) use ($smarty, $stock) {
        $user = '';
        $token = $app['security.token_storage']->getToken();
        if ($token !== null) {
            $user = $token->getUser();
        }
        $route = $request->attributes->get('_route');
        $smarty->assign('username', $user);
        $smarty->assign('sites', $stock->getSites());
        $smarty->assign('locations', $stock->getLocations());
        $smarty->assign('categories', $stock->getCategories());
        $smarty->assign('route', '/'.$route);
        $smarty->assign('msg', implode(getAllMessages($app)));
    });

    $systemRoutes->addRoutes($storage);
    $authRoutes->addRoutes($storage);
    $itemRoutes->addRoutes($storage);
    $locationRoutes->addRoutes($storage);
    $categoryRoutes->addRoutes($storage);
    $siteRoutes->addRoutes($storage);
    $userRoutes->addRoutes($storage);

    $app->boot();
    $app->run();

    function getAllMessages($app) {
        $types = array('error', 'warning', 'info', 'success');
        $messages = array();
        foreach ($types as $type) {
            foreach(getMessages($app, $type) as $message) {
                $messages[] = $message;
            }
        }
        return $messages;
    }

    function getMessages($app, string $type): array {
        $messages = array();
        foreach ($app['session']->getFlashBag()->get($type, array()) as $message) {
            $messages[] = message($type, $message);
        }
        return $messages;
    }

    function successMessage(string $message): string {
        return message('success', $message);
    }

    function infoMessage(string $message): string {
        return message('info', $message);
    }

    function warningMessage(string $message): string {
        return message('warning', $message);
    }

    function errorMessage(string $message): string {
        return message('danger', $message);
    }

    function message(string $type, string $message): string {
        if ($type == 'error') {
            $type = 'danger';
        }
        return '
            <div class="alert alert-'.$type.' alert-dismissible fade show" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
            '.$message.'
            </div>
        ';
    }
