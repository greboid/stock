<?php
    declare(strict_types=1);

    namespace greboid\stock;

    use \Exception;
    use \greboid\stock\Stock;
    use \greboid\stock\ItemRoutes;
    use \greboid\stock\LocationRoutes;
    use \greboid\stock\CategoryRoutes;
    use \greboid\stock\SiteRoutes;
    use \Bramus\Router\Router;
    use \Aura\Auth\Auth;
    use \Smarty;
    use ICanBoogie\Storage\RunTimeStorage;

    class AuthRoutes {

        public function addRoutes(Router $router, RunTimeStorage $storage): void {
            $auth = $storage->retrieve('auth');
            $login_service = $storage->retrieve('loginService');
            $smarty = $storage->retrieve('smarty');
            $stock = $storage->retrieve('stock');
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
            $router->post('/auth/login', function() use ($smarty, $auth, $login_service) {
                try {
                    $login_service->login($auth, array(
                        'username' => $_POST['lg_username'],
                        'password' => $_POST['lg_password'],
                    ));
                    header('Location: /');
                    die('Login Success!');
                } catch (\Aura\Auth\Exception\UsernameMissing $e) {
                  die('No username specified.');
                } catch (\Aura\Auth\Exception\PasswordMissing $e) {
                  die('No Password specified.');
                } catch (\Aura\Auth\Exception\MultipleMatches $e) {
                  die('Multiple accounts found.');
                } catch (\Aura\Auth\Exception\UsernameNotFound $e) {
                  die('Username not found.');
                } catch (\Aura\Auth\Exception\PasswordIncorrect $e) {
                  die('Incorrect Password: '.password_hash($_POST['lg_password'], PASSWORD_DEFAULT));
                } catch (\Aura\Auth\Exception\ConnectionFailed $e) {
                  die('Connection failed to whatever the hell.');
                } catch (\Aura\Auth\Exception\BindFailed $e) {
                  die('LDAP bind failed.');
                } catch (InvalidLoginException $e) {
                  die('Invalid login exception.');
                }
            });
            $router->get('/auth/register', function() use ($smarty) {
                $smarty->display('register.tpl');
            });
            $router->get('/auth/reset', function() use ($smarty) {
                $smarty->display('passwordreset.tpl');
            });
            $router->get('/auth/logout', function() use ($smarty, $storage) {
                $storage->retrieve('logoutService')->logout($storage->retrieve('auth'));
                header('Location: /');
            });
        }
    }
