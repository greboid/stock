<?php
    declare(strict_types=1);

    namespace greboid\stock;

    use \Exception;
    use \Silex\Application;

    class SystemRoutes {

        public function addRoutes(Application $app): void {

            $app->get('/', function(Application $app) {
                try {
                    return $app->redirect('/site/all');
                } catch (Exception $e) {
                    return $app['twig']->render('500.tpl', array(
                        'error' => $e->getMessage(),
                    ));
                }
            });
        }
    }
