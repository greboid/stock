<?php
    declare(strict_types=1);

    namespace greboid\stock;

    use \Exception;
    use \Bramus\Router\Router;
    use \Smarty;
    use \ICanBoogie\Storage\RunTimeStorage;

    class UserRoutes {

        public function addRoutes(RunTimeStorage $storage): void {
            $router = $storage->retrieve('router');
            $smarty = $storage->retrieve('smarty');
            $stock = $storage->retrieve('stock');
            $msg = $storage->retrieve('flash');
            $auth = $storage->retrieve('auth');

            $router->get('/user/profile', function() use($smarty, $stock, $auth) {
                try {
                    $smarty->assign('username', $auth->getUserName());
                    $smarty->assign('userdata', $auth->getUserData());
                    $smarty->display('profile.tpl');
                } catch (Exception $e) {
                    $smarty->assign('error', $e->getMessage());
                    $smarty->display('500.tpl');
                }
            });
        }
    }
