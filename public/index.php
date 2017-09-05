<?php
    declare(strict_types=1);

    require_once('../vendor/autoload.php');
    require_once('../configs/production.php');

    use \greboid\stock\StockApplication;
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

    use \Silex\Application;
    use \Symfony\Component\HttpFoundation\Request;
    use \Symfony\Component\HttpFoundation\Session\Session;
    use \Silex\Provider\SessionServiceProvider;
    use \Silex\Provider\SecurityServiceProvider;
    use \Silex\Provider\SwiftmailerServiceProvider;
    use \Silex\Provider\FormServiceProvider;

    $itemRoutes = new ItemRoutes();
    $locationRoutes = new LocationRoutes();
    $categoryRoutes = new CategoryRoutes();
    $siteRoutes = new SiteRoutes();
    $systemRoutes = new SystemRoutes();
    $authRoutes = new AuthRoutes();
    $userRoutes = new UserRoutes();
    $error = false;
    $database = null;
    try {
        $database = new Database();
    } catch (Exception $e) {
        if ($e->getMessage() == 'Unable to connect to the database.') {
            return $app['twig']->render('500.tpl', array(
                'error' => 'The database connection settings are wrong, please check the config',
            ));
        } else {
            return $app['twig']->render('install.tpl', array(
                'error' => $e->getMessage(),
            ));
        }
        exit();
    }
    $stock = new Stock($database);

    $session = new Session();
    $app = new StockApplication();
    $app['db'] = $database->getPDO();
    $app['stock'] = $stock;
    $app['pdo'] = $database->getPDO();
    $app['database'] = $database;
    $app['session'] = $session;
    if (DEBUG) {
        $app['debug'] = true;
    }
    $app->register(new Silex\Provider\TwigServiceProvider(), array(
        'twig.path' => __DIR__.'/../templates',
        'twig.options' => array('debug' => true),
    ));
    $app->extend('twig', function($twig, $app) {
        $twig->addFilter(new Twig_Filter('repeat', 'str_reapeat'));
        $twig->addFilter(new Twig_Filter('count', 'count'));
        $twig->addFunction(new Twig_Function('repeat', 'str_repeat'));
        $twig->addFunction(new Twig_Function('var_dump', 'var_dump'));
        $twig->addFunction(new Twig_Function('truncate', function ($text, $chars = 25) {
            $text = $text." ";
            $text = substr($text,0,$chars);
            $text = substr($text,0,strrpos($text,' '));
            $text = $text."...";
            return $text;
        }));
        return $twig;
    });
    $app->register(new FormServiceProvider());
    $app->register(new Silex\Provider\ValidatorServiceProvider());
    $app['swiftmailer.use_spool'] = false;
    $app->register(new SwiftmailerServiceProvider(), [
        'swiftmailer.options' => [
            'host' => SMTP_SERVER,
            'port' => SMTP_PORT,
            'username' => SMTP_USERNAME,
            'password' => SMTP_PASSWORD,
            'encryption' => 'tls',
            'auth_mode' => 'login',
        ],
    ]);
    $app->register(new SessionServiceProvider());
    $app->register(new SecurityServiceProvider(), array(
        'security.firewalls' => array(
            'login' => array(
                'pattern' => '^/auth/login$',
                'anonymous' => true,
            ),
            'verifyemail' => array(
                'pattern' => '^/auth/verifyemail/.*$',
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


    $app->error(function (\Exception $e, Request $request, $code) use($app) {
        switch ($code) {
            case 404:
                return $app['twig']->render('404.tpl', array());
            default:
                return $app['twig']->render('500.tpl', array(
                    'error' => $e->getMessage(),
                ));
        }
    });
    $app->before(function (Request $request, Application $app) use ($stock) {
        $user = '';
        $token = $app['security.token_storage']->getToken();
        if ($token !== null) {
            $user = $token->getUser();
        }
        $route = $request->attributes->get('_route');
        $app['twig']->addGlobal('max_stock', MAX_STOCK);
        $app['twig']->addGlobal('username', $user);
        $app['twig']->addGlobal('sites', $stock->getSites());
        $app['twig']->addGlobal('locations', $stock->getLocations());
        $app['twig']->addGlobal('categories', $stock->getCategories());
        $app['twig']->addGlobal('route', '/'.$route);
        $app['twig']->addGlobal('msg', implode(getAllMessages($app)));
    });

    $systemRoutes->addRoutes($app);
    $authRoutes->addRoutes($app);
    $itemRoutes->addRoutes($app);
    $locationRoutes->addRoutes($app);
    $categoryRoutes->addRoutes($app);
    $siteRoutes->addRoutes($app);
    $userRoutes->addRoutes($app);

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
