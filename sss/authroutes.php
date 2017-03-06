<?php
    declare(strict_types=1);

    namespace greboid\stock;

    use \Exception;
    use \greboid\stock\Stock;
    use \greboid\stock\ItemRoutes;
    use \greboid\stock\LocationRoutes;
    use \greboid\stock\CategoryRoutes;
    use \greboid\stock\SiteRoutes;
    use \Bramus\Router\Router;
    use \Aura\Auth\Auth;
    use \Smarty;
    use ICanBoogie\Storage\RunTimeStorage;

    class AuthRoutes {

        public function addRoutes(Router $router, RunTimeStorage $storage): void {
            $auth = $storage->retrieve('auth');
            $login_service = $storage->retrieve('loginService');
            $smarty = $storage->retrieve('smarty');
            $stock = $storage->retrieve('stock');
            $router->before('GET', '(.*)', function($route) use ($smarty, $auth) {
                if (preg_match('#^(?!auth).*#', $route)) {
                    if ($auth->getStatus() == 'ANON') {
                        $smarty->display('login.tpl');
                        exit();
                    }
                }
            });
            $router->get('/auth/login', function() use ($smarty) {
                $smarty->display('login.tpl');
            });
            $router->post('/auth/login', function() use ($smarty, $auth, $login_service) {
                try {
                    $login_service->login($auth, array(
                        'username' => $_POST['lg_username'],
                        'password' => $_POST['lg_password'],
                    ));
                    header('Location: /');
                } catch (Exception $e) {
                  header('Location: /auth/login');
                }
            });
            $router->get('/auth/register', function() use ($smarty) {
                $smarty->display('register.tpl');
            });
            $router->get('/auth/reset', function() use ($smarty) {
                $smarty->display('passwordreset.tpl');
            });
            $router->get('/auth/logout', function() use ($smarty, $storage) {
                try {
                    $storage->retrieve('logoutService')->logout($storage->retrieve('auth'));
                } catch (Exception $e) {
                    die($e);
                }
                header('Location: /');
            });
        }
    }
