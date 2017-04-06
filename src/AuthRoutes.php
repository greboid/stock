<?php
    declare(strict_types=1);

    namespace greboid\stock;

    use \Exception;
    use \PDO;
    use \Bramus\Router\Router;
    use \Smarty;
    use \ICanBoogie\Storage\RunTimeStorage;
    use \Silex\Application;
    use \Symfony\Component\HttpFoundation\Request;

    class AuthRoutes {

        public function addRoutes(RunTimeStorage $storage): void {
            $app = $storage->retrieve('app');
            $loginService = $storage->retrieve('loginService');
            $smarty = $storage->retrieve('smarty');
            $stock = $storage->retrieve('stock');
            $msg = $storage->retrieve('flash');
            $pdo = $storage->retrieve('pdo');

            $app->get('/auth/login', function() use ($smarty, $app) {
                $smarty->assign('loginMessage', LOGIN_MESSAGE);
                $smarty->assign('msg', $app['security.last_error']($app['request_stack']->getCurrentRequest()));
                return $smarty->fetch('login.tpl');
            })->bind('login_get');
            $app->get('/auth/loggedout', function() use ($smarty, $app) {
                return $smarty->fetch('loggedout.tpl');
            });
            $app->get('/auth/register', function() use ($smarty) {
                return $smarty->fetch('register.tpl');
            });
            $app->get('/auth/reset', function() use ($smarty) {
                return $smarty->fetch('passwordreset.tpl');
            });
            $app->get('/auth/logout', function() use ($smarty, $storage, $msg, $app) {
                try {
                    $storage->retrieve('logoutService')->logout($storage->retrieve('auth'));
                } catch (Exception $e) {
                    $app['session']->getFlashBag()->add('error', 'Unable to logout: '.$e->getMessage());
                }
                return $app->redirect('/');
            })->bind('login_logout');
            $app->get('/auth/verifyemail/{token}', function($token) use ($smarty, $pdo, $msg) {
                try {
                    $stmt = $pdo->prepare('
                        SELECT COALESCE((SELECT id FROM accounts WHERE verify_token=:token), 0) as id
                    ');
                    $stmt->bindValue(':token', $token, PDO::PARAM_STR);
                    $stmt->execute();
                    $userID = $stmt->fetchObject()->id;
                    if ($userID == 0) {
                        $smarty->assign('error', 'Unable to find token.');
                        return $smarty->fetch('500.tpl');
                    } else {
                        return $smarty->fetch('verifytoken.tpl');
                    }
                } catch (Exception $e) {
                    $app['session']->getFlashBag()->add('error', 'Unable to verify email: '.$e->getMessage());
                }
            })->bind('login_verify_get');
            $app->post('/auth/verifyemail/{token}', function($token) use ($smarty, $pdo, $storage, $app) {
                $password = filter_input(INPUT_POST, 'newpassword', FILTER_UNSAFE_RAW);
                try {
                    $stmt = $pdo->prepare('
                        SELECT COALESCE((SELECT id FROM accounts WHERE verify_token=:token), 0) as id
                    ');
                    $stmt->bindValue(':token', $token, PDO::PARAM_STR);
                    $stmt->execute();
                    $userID = $stmt->fetchObject()->id;
                    if ($userID == 0) {
                        $smarty->assign('error', 'Unable to find token.');
                        return $smarty->fetch('500.tpl');
                    } else {
                        $stmt = $pdo->prepare('
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
