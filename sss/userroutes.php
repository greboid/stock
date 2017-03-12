<?php
    declare(strict_types=1);

    namespace greboid\stock;

    use \Exception;
    use \Bramus\Router\Router;
    use \Smarty;
    use \ICanBoogie\Storage\RunTimeStorage;

    class UserRoutes {

        public function addRoutes(RunTimeStorage $storage): void {
            $router = $storage->retrieve('router');
            $smarty = $storage->retrieve('smarty');
            $stock = $storage->retrieve('stock');
            $msg = $storage->retrieve('flash');
            $auth = $storage->retrieve('auth');
            $pdo = $storage->retrieve('pdo');

            $router->get('/user/profile', function() use($smarty, $stock, $auth) {
                try {
                    $smarty->assign('username', $auth->getUserName());
                    $smarty->assign('userdata', $auth->getUserData());
                    $smarty->display('profile.tpl');
                } catch (Exception $e) {
                    $smarty->assign('error', $e->getMessage());
                    $smarty->display('500.tpl');
                }
            });
            $router->get('/user/checkemail', function() use($smarty, $stock, $auth, $pdo) {
                $email = filter_input(INPUT_POST, "email", FILTER_UNSAFE_RAW);
                if ($email == null) {
                    $email = filter_input(INPUT_GET, "email", FILTER_UNSAFE_RAW);
                }
                $username = filter_input(INPUT_POST, "username", FILTER_UNSAFE_RAW);
                if ($username == null) {
                    $username = filter_input(INPUT_GET, "username", FILTER_UNSAFE_RAW);
                }
                $smarty->assign('output', $username.' - '.$email);
                $sql = 'SELECT COUNT(*) as count FROM '.ACCOUNTS_TABLE.' WHERE email=:email AND username != :username';
                $stmt = $pdo->prepare($sql);
                $stmt->bindParam(':username', $username);
                $stmt->bindParam(':email', $email);
                $stmt->execute();
                $row = $stmt->fetchObject();
                if ($row->count == 0) {
                    $smarty->assign('output', "true");
                } else {
                    $smarty->assign('output', "Email address is in use.");
                }
                $smarty->display('outputjson.tpl');
            });
        }
    }
