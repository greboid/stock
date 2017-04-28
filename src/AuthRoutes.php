<?php
    declare(strict_types=1);

    namespace greboid\stock;

    use \Exception;
    use \PDO;
    use \Silex\Application;

    class AuthRoutes {

        public function addRoutes(Application $app): void {

            $app->get('/auth/login', function(Application $app) {
                return $app->render('login.tpl', [
                        'loginMessage' => LOGIN_MESSAGE,
                        'msg' => $app['security.last_error']($app['request_stack']->getCurrentRequest()),
                    ]);
            })->bind('login_get');
            $app->get('/auth/loggedout', function(Application $app) {
                return $app['twig']->render('loggedout.tpl', array());
            });
            $app->get('/auth/verifyemail/{token}', function(Application $app, $token) {
                try {
                    $stmt = $app['pdo']->prepare('
                        SELECT COALESCE((SELECT id FROM accounts WHERE verify_token=:token), 0) as id
                    ');
                    $stmt->bindValue(':token', $token, PDO::PARAM_STR);
                    $stmt->execute();
                    $userID = $stmt->fetchObject()->id;
                    if ($userID == 0) {
                        return $app['twig']->render('500.tpl', array(
                            'error' => 'Unable to find token.',
                        ));
                    } else {
                        return $app['twig']->render('verifytoken.tpl', array());
                    }
                } catch (Exception $e) {
                    $app['session']->getFlashBag()->add('error', 'Unable to verify email: '.$e->getMessage());
                }
            })->bind('login_verify_get');
            $app->post('/auth/verifyemail/{token}', function(Application $app, $token) {
                $password = filter_input(INPUT_POST, 'newpassword', FILTER_UNSAFE_RAW);
                try {
                    $stmt = $app['pdo']->prepare('
                        SELECT COALESCE((SELECT id FROM accounts WHERE verify_token=:token), 0) as id
                    ');
                    $stmt->bindValue(':token', $token, PDO::PARAM_STR);
                    $stmt->execute();
                    $userID = $stmt->fetchObject()->id;
                    if ($userID == 0) {
                        return $app['twig']->render('500.tpl', array(
                            'error' => 'Unable to find token.',
                        ));
                    } else {
                        $stmt = $app['pdo']->prepare('
                            UPDATE accounts
                            SET password=:password, verified=1
                            WHERE verify_token=:token
                        ');
                        $stmt->bindValue('token', $token, PDO::PARAM_STR);
                        $stmt->bindValue('password', password_hash($password, PASSWORD_DEFAULT), PDO::PARAM_STR);
                        $stmt->execute();
                    }
                } catch (Exception $e) {
                    $app['session']->getFlashBag()->add('error', 'Unable to verify email: '.$e->getMessage());
                }
                return $app->redirect('/');
            })->bind('login_verify_post');
        }
    }
