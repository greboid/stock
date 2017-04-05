<?php
    declare(strict_types=1);

    namespace greboid\stock;

    use \Exception;
    use \Bramus\Router\Router;
    use \Smarty;
    use \ICanBoogie\Storage\RunTimeStorage;
    use \PDO;
    use \sweelix\guid\Guid;
    use \PHPMailer;

    class UserRoutes {

        public function addRoutes(RunTimeStorage $storage): void {
            $app = $storage->retrieve('app');
            $smarty = $storage->retrieve('smarty');
            $stock = $storage->retrieve('stock');
            $msg = $storage->retrieve('flash');
            $auth = $storage->retrieve('auth');
            $pdo = $storage->retrieve('pdo');

            $app->get('/user/profile', function() use($smarty, $stock, $auth, $pdo) {
                try {
                    $stmt = $pdo->prepare('SELECT email, name FROM '.ACCOUNTS_TABLE.' WHERE username=:username');
                    $stmt->bindValue(':username', $auth->getUserName());
                    $stmt->execute();
                    $userData = $stmt->fetch(PDO::FETCH_ASSOC);
                    $smarty->assign('username', $auth->getUserName());
                    $smarty->assign('userdata', $userData);
                    return $smarty->fetch('profile.tpl');
                } catch (Exception $e) {
                    $smarty->assign('error', $e->getMessage());
                    return $smarty->return('500.tpl');
                }
            });
            $app->get('/user/checkemail', function() use($smarty, $stock, $auth, $pdo) {
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
                return $smarty->fetch('outputjson.tpl');
            });
            $app->get('/user/checkusername', function() use($smarty, $stock, $auth, $pdo) {
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
                return $smarty->fetch('outputjson.tpl');
            });
            $app->post('/user/profile', function() use ($smarty, $pdo, $msg, $app) {
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
                return $app->redirect('/user/profile');
            });
            $app->post('/user/password', function() use ($smarty, $pdo, $msg, $auth, $app) {
                $username = filter_input(INPUT_POST, "username", FILTER_SANITIZE_STRING);
                $password = filter_input(INPUT_POST, "newpassword", FILTER_UNSAFE_RAW);
                $password = password_hash($password, PASSWORD_DEFAULT);
                if ($auth->getUsername() == $username) {
                    try {
                        $stmt = $pdo->prepare('UPDATE '.ACCOUNTS_TABLE.' SET password=:password WHERE username=:username');
                        $stmt->bindValue(':password', $password);
                        $stmt->bindValue(':username', $username);
                        $val = $stmt->execute();

                        $msg->info('Your details have been updated.');
                    } catch (Exception $e) {
                        $msg->error('Unable to update details: '.$e->getMessage());
                    }
                } else {
                    $msg->error('Unable to update another user\'s password');
                }
                return $app->redirect('/user/profile');
            });
            $app->get('/manage/users', function() use ($smarty, $pdo, $msg) {
                $stmt = $pdo->prepare('SELECT id, username, name, email, active from '.ACCOUNTS_TABLE);
                $stmt->execute();
                $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
                $smarty->assign('users', $results);
                return $smarty->fetch('manageusers.tpl');
            });
            $app->post('/add/user', function() use ($smarty, $pdo, $msg, $app) {
                $username = filter_input(INPUT_POST, "username", FILTER_SANITIZE_STRING);
                $email = filter_input(INPUT_POST, "email", FILTER_SANITIZE_EMAIL);
                $name = filter_input(INPUT_POST, "name", FILTER_SANITIZE_STRING);
                $active = filter_input(INPUT_POST, "active", FILTER_SANITIZE_NUMBER_INT);

                try {
                    $token = Guid::v4();
                    $stmt = $pdo->prepare('INSERT INTO '.ACCOUNTS_TABLE.'
                                          (username, name, email, active, verified, password, verify_token)
                                          VALUES (:username, :name, :email, :active, :verified, :password, :verifytoken);');
                    $stmt->bindValue(':username', $username);
                    $stmt->bindValue(':name', $name);
                    $stmt->bindValue(':email', $email);
                    $stmt->bindValue(':active', $active);
                    $stmt->bindValue(':password', '');
                    $stmt->bindValue(':verified', 0);
                    $stmt->bindValue(':verifytoken', $token);
                    $stmt->execute();
                } catch (Exception $e) {
                    $msg->error('Unable to add user: '.$e->getMessage());
                }
                if ($this->sendNewUserMail($username, $name, $email, $token) != '') {
                    $msg->error('New user email failed to send.');
                }
                return $app->redirect('/manage/users');
            });
            $app->post('/edit/user', function() use ($smarty, $pdo, $app) {
                try {
                    $userID = filter_input(INPUT_POST, "editID", FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE);
                    $userUsername = filter_input(INPUT_POST, "editUsername", FILTER_UNSAFE_RAW, FILTER_NULL_ON_FAILURE);
                    $userName = filter_input(INPUT_POST, "editName", FILTER_UNSAFE_RAW, FILTER_NULL_ON_FAILURE);
                    $userEmail = filter_input(INPUT_POST, "editEmail", FILTER_UNSAFE_RAW, FILTER_NULL_ON_FAILURE);
                    $userActive = filter_input(INPUT_POST, "active", FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE);
                    $this->editUser($pdo, $userID, $userUsername, $userName, $userEmail, $userActive);
                    return $app->redirect('/manage/users');
                } catch (Exception $e) {
                    $smarty->assign('error', $e->getMessage());
                    return $smarty->fetch('500.tpl');
                }
            });
            $app->post('/delete/user', function() use ($smarty, $pdo, $msg, $app) {
                $userid = filter_input(INPUT_POST, "userid", FILTER_SANITIZE_NUMBER_INT);
                try {
                    $stmt = $pdo->prepare('DELETE FROM '.ACCOUNTS_TABLE.' where id=:id;');
                    $stmt->bindValue(':id', $userid);
                    $stmt->execute();
                } catch (Exception $e) {
                    $msg->error('Unable to delete user: '.$e->getMessage());
                }
                return $app->redirect('/manage/users');
            });
            $app->post('/user/sendverification', function() use ($smarty, $pdo, $msg, $app) {
                $userid = filter_input(INPUT_POST, "userid", FILTER_SANITIZE_NUMBER_INT);
                try {
                    $stmt = $pdo->prepare('
                        SELECT username, name, email, verify_token
                        FROM '.ACCOUNTS_TABLE.'
                        WHERE id=:userID
                    ');
                    $stmt->bindValue(':userID', $userid, PDO::PARAM_INT);
                    $stmt->execute();
                    $result = $stmt->fetchObject();
                    if (empty($result->verify_token)) {
                        $token = Guid::v4();
                        $stmt = $pdo->prepare('
                            UPDATE '.ACCOUNTS_TABLE.'
                            SET verify_token=:token
                            WHERE id=:userID
                        ');
                        $stmt->bindValue(':token', $token);
                        $stmt->bindValue(':userID', $userid);
                        $stmt->execute();
                    } else {
                        $token = $result->verify_token;
                    }
                    if ($this->sendNewUserMail($result->username,
                                               $result->name,
                                               $result->email,
                                               $token) != '') {
                        $msg->error('New user email failed to send.');
                    }
                    return $app->redirect('/manage/users');
                } catch (Exception $e) {
                    $msg->error('Unable to delete user: '.$e->getMessage());
                }
            });
        }

        private function sendNewUserMail($username, $name, $email, $code): string {
            $mail = new PHPMailer;
            $mail->isSMTP();
            $mail->Host = SMTP_SERVER;
            $mail->SMTPAuth = true;
            $mail->Username = SMTP_USERNAME;
            $mail->Password = SMTP_PASSWORD;
            $mail->Port = SMTP_PORT;

            $mail->setFrom('noreply@'.$_SERVER['HTTP_HOST'], 'Stock System');
            $mail->addAddress($email, $name);

            $mail->isHTML(true);

            $mail->Subject = 'Welcome to the Stock System';
            $mail->Body    = 'Welcome to the stock system '.$name.'.  You username is '.$username.', but to continue you need to <a href="'.$this->getEmailLink($code).'">verify your account</a>.';
            $mail->AltBody = 'Welcome to the stock system '.$name.'.  You username is '.$username.', to continue you need to verify your account, by visiting '.$this->getEmailLink($code);

            if (!$mail->send()) {
                return $mail->ErrorInfo;
            } else {
                return '';
            }
        }

        private function getEmailLink($token) {
            return $this->getBaseURL().'/auth/verifyemail/'.$token;
        }

        private function getBaseURL() {
            $fulluri = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
            $total = strlen($fulluri);
            $path = strlen($this->getCurrentPath());
            return substr($fulluri, 0, $total-$path);
        }

        private function getCurrentPath() {
            $uri = substr($_SERVER['REQUEST_URI'], strlen((implode('/', array_slice(explode('/', $_SERVER['SCRIPT_NAME']), 0, -1)) . '/')));
            if (strstr($uri, '?')) {
                $uri = substr($uri, 0, strpos($uri, '?'));
            }
            return '/' . trim($uri, '/');
        }

        public function editUser(PDO $pdo, int $userID, string $userUsername, string $userName, string $userEmail, int $userActive): void {
            $statement = $pdo->prepare('
                UPDATE '.ACCOUNTS_TABLE.'
                set username=:username,
                    email=:name,
                    name=:email,
                    active=:active
                WHERE id=:id
            ');
            $statement->bindValue(':id', $userID, PDO::PARAM_INT);
            $statement->bindValue(':username', $userUsername, PDO::PARAM_STR);
            $statement->bindValue(':email', $userName, PDO::PARAM_INT);
            $statement->bindValue(':name', $userEmail, PDO::PARAM_INT);
            $statement->bindValue(':active', $userActive, PDO::PARAM_INT);
            $statement->execute();
        }
    }
