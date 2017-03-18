<?php
    declare(strict_types=1);

    namespace greboid\stock;

    use \Exception;
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
            $router->before('GET', '(.*)', function($route) use ($smarty, $auth) {
                if (preg_match('#^(?!auth).*#', $route)) {
                    if ($auth->getStatus() == 'ANON') {
                        $smarty->display('login.tpl');
                        exit();
                    }
                }
            });
            $router->get('/auth/login', function() use ($smarty) {
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
                    $msg->info('You are now logged in: '.$auth->getUserData()['name']);
                    header('Location: /');
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
            $router->get('/auth/logout', function() use ($smarty, $storage) {
                try {
                    $storage->retrieve('logoutService')->logout($storage->retrieve('auth'));
                } catch (Exception $e) {
                    die($e);
                }
                header('Location: /');
            });
            $router->get('/auth/verifyemail/(.*)', function($token) use ($smarty, $storage) {
                die($token);
            });
        }
    }
