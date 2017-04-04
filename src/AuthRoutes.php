<?php
    declare(strict_types=1);

    namespace greboid\stock;

    use \Exception;
    use \PDO;
    use \Bramus\Router\Router;
    use \Aura\Auth\Auth;
    use \Smarty;
    use \ICanBoogie\Storage\RunTimeStorage;

    class AuthRoutes {

        public function addRoutes(Router $router, RunTimeStorage $storage): void {
            $auth = $storage->retrieve('auth');
            $loginService = $storage->retrieve('loginService');
            $smarty = $storage->retrieve('smarty');
            $stock = $storage->retrieve('stock');
            $msg = $storage->retrieve('flash');
            $pdo = $storage->retrieve('pdo');

            $router->get('/auth/login', function() use ($smarty) {
                $smarty->assign('loginMessage', LOGIN_MESSAGE);
                $smarty->display('login.tpl');
            });
            $router->post('/auth/login', function() use ($smarty, $auth, $loginService, $msg) {
                $username = filter_input(INPUT_POST, "username", FILTER_UNSAFE_RAW, FILTER_NULL_ON_FAILURE);
                $password = filter_input(INPUT_POST, "password", FILTER_UNSAFE_RAW, FILTER_NULL_ON_FAILURE);
                try {
                    $loginService->login($auth, array(
                        'username' => $username,
                        'password' => $password,
                    ));
                    $msg->info('You are now logged in: '.htmlspecialchars($auth->getUserData()['name']));
                    if (isset($SESSION['postauthredirect'])) {
                        header('Location: /'.SESSION['postauthredirect']);
                    } else {
                        header('Location: /');
                    }
                } catch (\Aura\Auth\Exception\UsernameMissing $e) {
                    $msg->error('You must specify a username.');
                    header('Location: /auth/login');
                } catch (\Aura\Auth\Exception\PasswordMissing $e) {
                    $msg->error('You must specify a password.');
                    header('Location: /auth/login');
                } catch (\Aura\Auth\Exception\MultipleMatches $e) {
                    $msg->error('Unable to login, multiple user matches.');
                    header('Location: /auth/login');
                } catch (\Aura\Auth\Exception\UsernameNotFound $e) {
                    $msg->error('Incorrect login details.');
                    header('Location: /auth/login');
                } catch (\Aura\Auth\Exception\PasswordIncorrect $e) {
                    $msg->error('Incorrect login details.');
                    header('Location: /auth/login');
                } catch (\Aura\Auth\Exception\ConnectionFailed $e) {
                    $msg->error('Unable to connect to authentication source.');
                    header('Location: /auth/login');
                } catch (\Aura\Auth\Exception\BindFailed $e) {
                    $msg->error('Unable to connect to authentication source.');
                    header('Location: /auth/login');
                } catch (InvalidLoginException $e) {
                    $msg->error('Incorrect login details.');
                    header('Location: /auth/login');
                }
            });
            $router->get('/auth/register', function() use ($smarty) {
                $smarty->display('register.tpl');
            });
            $router->get('/auth/reset', function() use ($smarty) {
                $smarty->display('passwordreset.tpl');
            });
            $router->get('/auth/logout', function() use ($smarty, $storage, $msg) {
                try {
                    $storage->retrieve('logoutService')->logout($storage->retrieve('auth'));
                } catch (Exception $e) {
                    $msg->error('Unable to logout: '.$e->getMessage());
                }
                header('Location: /');
            });
            $router->get('/auth/verifyemail/(.*)', function($token) use ($smarty, $pdo, $msg) {
                try {
                    $stmt = $pdo->prepare('
                        SELECT COALESCE((SELECT id FROM accounts WHERE verify_token=:token), 0) as id
                    ');
                    $stmt->bindValue(':token', $token, PDO::PARAM_STR);
                    $stmt->execute();
                    $userID = $stmt->fetchObject()->id;
                    if ($userID == 0) {
                        $smarty->assign('error', 'Unable to find token.');
                        $smarty->display('500.tpl');
                    } else {
                        $smarty->display('verifytoken.tpl');
                    }
                } catch (Exception $e) {
                    $msg->error('Unable to verify email: '.$e->getMessage());
                }
            });
            $router->post('/auth/verifyemail/(.*)', function($token) use ($smarty, $pdo, $storage) {
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
                        $smarty->display('500.tpl');
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
                header('Location: /');
            });
        }
    }
