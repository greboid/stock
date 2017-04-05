<?php
    declare(strict_types=1);

    namespace greboid\stock;

    use \Exception;
    use \PDO;
    use \Bramus\Router\Router;
    use \Aura\Auth\Auth;
    use \Smarty;
    use \ICanBoogie\Storage\RunTimeStorage;
    use \Silex\Application;
    use \Symfony\Component\HttpFoundation\Request;
    use \Aura\Auth\Status;

    class AuthRoutes {

        public function addRoutes(RunTimeStorage $storage): void {
            $app = $storage->retrieve('app');
            $auth = $storage->retrieve('auth');
            $loginService = $storage->retrieve('loginService');
            $smarty = $storage->retrieve('smarty');
            $stock = $storage->retrieve('stock');
            $msg = $storage->retrieve('flash');
            $pdo = $storage->retrieve('pdo');

            $app->before(function (Request $request, Application $app) use ($auth) {
                $route = $request->attributes->get('_route');
                if ($auth->getStatus() !== Status::VALID && substr($route, 0, 5) !== 'login') {
                    return $app->redirect('/auth/login');
                }
            });

            $app->get('/auth/login', function() use ($smarty) {
                $smarty->assign('loginMessage', LOGIN_MESSAGE);
                return $smarty->fetch('login.tpl');
            })->bind('login_get');
            $app->post('/auth/login', function() use ($smarty, $auth, $loginService, $msg, $app) {
                $username = filter_input(INPUT_POST, "username", FILTER_UNSAFE_RAW, FILTER_NULL_ON_FAILURE);
                $password = filter_input(INPUT_POST, "password", FILTER_UNSAFE_RAW, FILTER_NULL_ON_FAILURE);
                try {
                    $loginService->login($auth, array(
                        'username' => $username,
                        'password' => $password,
                    ));
                    $msg->info('You are now logged in: '.htmlspecialchars($auth->getUserData()['name']));
                    if (isset($SESSION['postauthredirect'])) {
                        return $app->redirect('/'.SESSION['postauthredirect']);
                    } else {
                        return $app->redirect('/');
                    }
                } catch (\Aura\Auth\Exception\UsernameMissing $e) {
                    $msg->error('You must specify a username.');
                    return $app->redirect('/auth/login');
                } catch (\Aura\Auth\Exception\PasswordMissing $e) {
                    $msg->error('You must specify a password.');
                    return $app->redirect('/auth/login');
                } catch (\Aura\Auth\Exception\MultipleMatches $e) {
                    $msg->error('Unable to login, multiple user matches.');
                    return $app->redirect('/auth/login');
                } catch (\Aura\Auth\Exception\UsernameNotFound $e) {
                    $msg->error('Incorrect login details.');
                    return $app->redirect('/auth/login');
                } catch (\Aura\Auth\Exception\PasswordIncorrect $e) {
                    $msg->error('Incorrect login details.');
                    return $app->redirect('/auth/login');
                } catch (\Aura\Auth\Exception\ConnectionFailed $e) {
                    $msg->error('Unable to connect to authentication source.');
                    return $app->redirect('/auth/login');
                } catch (\Aura\Auth\Exception\BindFailed $e) {
                    $msg->error('Unable to connect to authentication source.');
                    return $app->redirect('/auth/login');
                } catch (InvalidLoginException $e) {
                    $msg->error('Incorrect login details.');
                    return $app->redirect('/auth/login');
                }
            })->bind('login_post');
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
                    $msg->error('Unable to logout: '.$e->getMessage());
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
                    $msg->error('Unable to verify email: '.$e->getMessage());
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
                    $msg->error('Unable to verify email: '.$e->getMessage());
                }
                return $app->redirect('/');
            })->bind('login_verify_post');
        }
    }
