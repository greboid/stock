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

    class AuthRoutes {

        public function addRoutes(Router $router, Smarty $smarty, Stock $stock, Auth $auth): void {
            $router->before('GET', '(.*)', function($route) use ($smarty, $auth) {
                if (preg_match('#^(?!auth).*#', $route)) {
                    if ($auth->getStatus() == 'ANON') {
                        $smarty->display('login.tpl');
                        exit();
                    }
                }
            });
            $router->get('/auth/login', function() use ($smarty, $stock) {
                $smarty->display('login.tpl');
            });
            $router->get('/auth/register', function() use ($smarty, $stock) {
                $smarty->display('register.tpl');
            });
            $router->get('/auth/reset', function() use ($smarty, $stock) {
                $smarty->display('passwordreset.tpl');
            });
        }
    }
