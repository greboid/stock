<?php
    declare(strict_types=1);

    namespace greboid\stock;

    use \Exception;
    use \Bramus\Router\Router;
    use \Smarty;
    use \ICanBoogie\Storage\RunTimeStorage;

    class SystemRoutes {

        public function addRoutes(Router $router, Smarty $smarty, RunTimeStorage $storage): void {
            $msg = $storage->retrieve('flash');
            $database = $storage->retrieve('database');
            $router->set404(function() use ($smarty) {
                if (strpos($_SERVER['REQUEST_URI'], '/auth') == 0) {
                    $smarty->display('404.tpl');
                    exit();
                }
                header('HTTP/1.1 404 Not Found');
                $smarty->display('404.tpl');
            });
            $router->get('/', function() use($smarty) {
                try {
                    header('Location: /site/all');
                } catch (Exception $e) {
                    $smarty->assign('error', $e->getMessage());
                    $smarty->display('500.tpl');
                }
            });
        }
    }
