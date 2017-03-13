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

            $router->get('/user/profile', function() use($smarty, $stock, $auth, $pdo) {
                try {
                    $stmt = $pdo->prepare('SELECT email, name FROM '.ACCOUNTS_TABLE.' WHERE username=:username');
                    $stmt->bindValue(':username', $auth->getUserName());
                    $stmt->execute();
                    $userData = $stmt->fetch(\PDO::FETCH_ASSOC);
                    $smarty->assign('username', $auth->getUserName());
                    $smarty->assign('userdata', $userData);
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
            $router->get('/user/checkusername', function() use($smarty, $stock, $auth, $pdo) {
                $username = filter_input(INPUT_POST, "username", FILTER_UNSAFE_RAW);
                if ($username == null) {
                    $username = filter_input(INPUT_GET, "username", FILTER_UNSAFE_RAW);
                }
                $sql = 'SELECT COUNT(*) as count FROM '.ACCOUNTS_TABLE.' WHERE username=:username';
                $stmt = $pdo->prepare($sql);
                $stmt->bindParam(':username', $username);
                $stmt->execute();
                $row = $stmt->fetchObject();
                if ($row->count == 0) {
                    $smarty->assign('output', "true");
                } else {
                    $smarty->assign('output', "Username is in use.");
                }
                $smarty->display('outputjson.tpl');
            });
            $router->post('/user/profile', function() use ($smarty, $pdo, $msg) {
                $email = filter_input(INPUT_POST, "email", FILTER_SANITIZE_EMAIL);
                $name = filter_input(INPUT_POST, "name", FILTER_SANITIZE_STRING);
                $username = filter_input(INPUT_POST, "username", FILTER_SANITIZE_STRING);
                try {
                    $stmt = $pdo->prepare('UPDATE '.ACCOUNTS_TABLE.' SET email=:email, name=:name WHERE username=:username');
                    $stmt->bindValue(':email', $email);
                    $stmt->bindValue(':name', $name);
                    $stmt->bindValue(':username', $username);
                    $stmt->execute();
                    $msg->info('Your details have been updated');
                } catch (Exception $e) {
                    $msg->error('Unable to update details: '.$e->getMessage());
                }
                header('Location: /user/profile');
            });
            $router->post('/user/password', function() use ($smarty, $pdo, $msg) {
                $username = filter_input(INPUT_POST, "username", FILTER_SANITIZE_STRING);
                $password = filter_input(INPUT_POST, "newpassword", FILTER_UNSAFE_RAW);
                $password = password_hash($password, PASSWORD_DEFAULT);
                try {
                    $stmt = $pdo->prepare('UPDATE '.ACCOUNTS_TABLE.' SET password=:password WHERE username=:username');
                    $stmt->bindValue(':password', $password);
                    $stmt->bindValue(':username', $username);
                    $val = $stmt->execute();

                    $msg->info('Your details have been updated.');
                } catch (Exception $e) {
                    $msg->error('Unable to update details: '.$e->getMessage());
                }
                header('Location: /user/profile');
            });
            $router->get('/manage/users', function() use ($smarty, $pdo, $msg){
                $smarty->assign('users', array());
                $smarty->display('manageusers.tpl');
            });
        }
    }
