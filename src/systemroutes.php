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
