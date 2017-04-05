<?php
    declare(strict_types=1);

    namespace greboid\stock;

    use \Exception;
    use \Bramus\Router\Router;
    use \Smarty;
    use \ICanBoogie\Storage\RunTimeStorage;

    class SystemRoutes {

        public function addRoutes(RunTimeStorage $storage): void {
            $app = $storage->retrieve('app');
            $smarty = $storage->retrieve('smarty');
            $stock = $storage->retrieve('stock');
            $msg = $storage->retrieve('flash');
            $database = $storage->retrieve('database');

            $app->get('/', function() use($smarty, $app){
                try {
                    return $app->redirect('/site/all');
                } catch (Exception $e) {
                    $smarty->assign('error', $e->getMessage());
                    return $smarty->fetch('500.tpl');
                }
            });
        }
    }
