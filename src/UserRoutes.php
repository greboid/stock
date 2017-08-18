<?php
    declare(strict_types=1);

    namespace greboid\stock;

    use \Exception;
    use \PDO;
    use \sweelix\guid\Guid;
    use \PHPMailer;
    use \Silex\Application;

    class UserRoutes {

        public function addRoutes(\Silex\Application $app): void {

            $app->get('/user/profile', function(Application $app) {
                try {
                    $token = $app['security.token_storage']->getToken();
                    $stmt = $app['pdo']->prepare('SELECT email, name FROM '.ACCOUNTS_TABLE.' WHERE username=:username');
                    $stmt->bindValue(':username', $token->getUser());
                    $stmt->execute();
                    $userData = $stmt->fetch(PDO::FETCH_ASSOC);
                    return $app['twig']->render('profile.tpl', array(
                        'username' => $token->getUser(),
                        'userdata' => $userData,
                    ));
                } catch (Exception $e) {
                    return $app->abort(500, $e->getMessage());
                }
            });
            $app->get('/user/checkemail', function(Application $app) {
                $email = filter_input(INPUT_POST, "email", FILTER_UNSAFE_RAW);
                if ($email == null) {
                    $email = filter_input(INPUT_GET, "email", FILTER_UNSAFE_RAW);
                }
                $username = filter_input(INPUT_POST, "username", FILTER_UNSAFE_RAW);
                if ($username == null) {
                    $username = filter_input(INPUT_GET, "username", FILTER_UNSAFE_RAW);
                }
                $sql = 'SELECT COUNT(*) as count FROM '.ACCOUNTS_TABLE.' WHERE email=:email AND username != :username';
                $stmt = $app['pdo']->prepare($sql);
                $stmt->bindParam(':username', $username);
                $stmt->bindParam(':email', $email);
                $stmt->execute();
                $row = $stmt->fetchObject();
                $output = '';
                if ($row->count == 0) {
                    $output = 'true';
                } else {
                    $output = 'Email Address is in use.';
                }
                return $app['twig']->render('outputjson.tpl', array(
                    'output' => $output,
                ));
            });
            $app->get('/user/checkusername', function(Application $app) {
                $username = filter_input(INPUT_POST, "username", FILTER_UNSAFE_RAW);
                if ($username == null) {
                    $username = filter_input(INPUT_GET, "username", FILTER_UNSAFE_RAW);
                }
                $sql = 'SELECT COUNT(*) as count FROM '.ACCOUNTS_TABLE.' WHERE username=:username';
                $stmt = $app['pdo']->prepare($sql);
                $stmt->bindParam(':username', $username);
                $stmt->execute();
                $row = $stmt->fetchObject();
                $output = '';
                if ($row->count == 0) {
                    $output = 'true';
                } else {
                    $output = 'Username is in use.';
                }
                return $app['twig']->render('outputjson.tpl', array(
                    'output' => $output,
                ));
            });
            $app->post('/user/profile', function(Application $app) {
                $email = filter_input(INPUT_POST, "email", FILTER_SANITIZE_EMAIL);
                $name = filter_input(INPUT_POST, "name", FILTER_SANITIZE_STRING);
                $username = filter_input(INPUT_POST, "username", FILTER_SANITIZE_STRING);
                try {
                    $stmt = $app['pdo']->prepare('UPDATE '.ACCOUNTS_TABLE.' SET email=:email, name=:name WHERE username=:username');
                    $stmt->bindValue(':email', $email);
                    $stmt->bindValue(':name', $name);
                    $stmt->bindValue(':username', $username);
                    $stmt->execute();
                    $app['session']->getFlashBag()->add('info', 'Your details have been updated');
                } catch (Exception $e) {
                    $app['session']->getFlashBag()->add('error', 'Unable to update details: '.$e->getMessage());
                }
                return $app->redirect('/user/profile');
            });
            $app->post('/user/password', function(Application $app) {
                $username = filter_input(INPUT_POST, "username", FILTER_SANITIZE_STRING);
                $password = filter_input(INPUT_POST, "newpassword", FILTER_UNSAFE_RAW);
                $password = password_hash($password, PASSWORD_DEFAULT);
                $token = $app['security.token_storage']->getToken();
                if ($token == null) {
                    return $app->redirect('/user/profile');
                }
                if ($token->getUser() == $username) {
                    try {
                        $stmt = $app['pdo']->prepare('UPDATE '.ACCOUNTS_TABLE.' SET password=:password WHERE username=:username');
                        $stmt->bindValue(':password', $password);
                        $stmt->bindValue(':username', $username);
                        $val = $stmt->execute();

                        $app['session']->getFlashBag()->add('info', 'Your details have been updated.');
                    } catch (Exception $e) {
                        $app['session']->getFlashBag()->add('error', 'Unable to update details: '.$e->getMessage());
                    }
                } else {
                    $app['session']->getFlashBag()->add('error', 'Unable to update another user\'s password');
                }
                return $app->redirect('/user/profile');
            });
            $app->get('/user/manage', function(Application $app) {
                $stmt = $app['pdo']->prepare('SELECT id, username, name, email, active from '.ACCOUNTS_TABLE);
                $stmt->execute();
                $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
                return $app['twig']->render('manageusers.tpl', array(
                    'users' => $results,
                ));
            });
            $app->post('/user/add', function(Application $app) {
                $username = filter_input(INPUT_POST, "username", FILTER_SANITIZE_STRING);
                $email = filter_input(INPUT_POST, "email", FILTER_SANITIZE_EMAIL);
                $name = filter_input(INPUT_POST, "name", FILTER_SANITIZE_STRING);
                $active = filter_input(INPUT_POST, "active", FILTER_SANITIZE_NUMBER_INT);

                try {
                    $token = Guid::v4();
                    $stmt = $app['pdo']->prepare('INSERT INTO '.ACCOUNTS_TABLE.'
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
                    $app['session']->getFlashBag()->add('error', 'Unable to add user: '.$e->getMessage());
                }
                if ($this->sendNewUserMail($app, $username, $name, $email, $token) != '') {
                    $app['session']->getFlashBag()->add('error', 'New user email failed to send.');
                }
                return $app->redirect('/user/manage');
            });
            $app->post('/user/edit', function(Application $app) {
                try {
                    $userID = filter_input(INPUT_POST, "editID", FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE);
                    $userUsername = filter_input(INPUT_POST, "editUsername", FILTER_UNSAFE_RAW, FILTER_NULL_ON_FAILURE);
                    $userName = filter_input(INPUT_POST, "editName", FILTER_UNSAFE_RAW, FILTER_NULL_ON_FAILURE);
                    $userEmail = filter_input(INPUT_POST, "editEmail", FILTER_UNSAFE_RAW, FILTER_NULL_ON_FAILURE);
                    $userActive = filter_input(INPUT_POST, "active", FILTER_VALIDATE_INT, FILTER_NULL_ON_FAILURE);
                    $this->editUser($app['pdo'], $userID, $userUsername, $userName, $userEmail, $userActive);
                    return $app->redirect('/user/manage');
                } catch (Exception $e) {
                    return $app->abort(500, $e->getMessage());
                }
            });
            $app->post('/user/delete', function(Application $app) {
                $userid = filter_input(INPUT_POST, "userid", FILTER_SANITIZE_NUMBER_INT);
                try {
                    $stmt = $app['pdo']->prepare('DELETE FROM '.ACCOUNTS_TABLE.' where id=:id;');
                    $stmt->bindValue(':id', $userid);
                    $stmt->execute();
                } catch (Exception $e) {
                    $app['session']->getFlashBag()->add('error', 'Unable to delete user: '.$e->getMessage());
                }
                return $app->redirect('/user/manage');
            });
            $app->post('/user/sendverification', function(Application $app) {
                $userid = filter_input(INPUT_POST, "userid", FILTER_SANITIZE_NUMBER_INT);
                try {
                    $stmt = $app['pdo']->prepare('
                        SELECT username, name, email, verify_token
                        FROM '.ACCOUNTS_TABLE.'
                        WHERE id=:userID
                    ');
                    $stmt->bindValue(':userID', $userid, PDO::PARAM_INT);
                    $stmt->execute();
                    $result = $stmt->fetchObject();
                    if (empty($result->verify_token)) {
                        $token = Guid::v4();
                        $stmt = $app['pdo']->prepare('
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
                    if ($this->sendNewUserMail($app,
                                               $result->username,
                                               $result->name,
                                               $result->email,
                                               $token) != '') {
                        $app['session']->getFlashBag()->add('error', 'New user email failed to send.');
                    }
                } catch (Exception $e) {
                    $app['session']->getFlashBag()->add('error', 'Unable to send verification: '.$e->getMessage());
                }
                return $app->redirect('/user/manage');
            });
        }

        private function sendNewUserMail($app, $username, $name, $email, $code): string {
            $message = \Swift_Message::newInstance()
                ->setSubject('Welcome to the Stock System')
                ->setFrom(array('noreply@'.$_SERVER['HTTP_HOST'] =>'Stock System'))
                ->setTo(array($email => $name))
                ->setBody('Welcome to the stock system '.$name.'.  You username is '
                          .$username.', to continue you need to verify your account, by visiting '
                          .$this->getEmailLink($code))
                ->addPart('Welcome to the stock system '.$name.'.  You username is '
                          .$username.', but to continue you need to <a href="'
                          .$this->getEmailLink($code).'">verify your account</a>.', 'text/html');
            $result = $app['mailer']->send($message, $failures);
            $app['swiftmailer.spooltransport']
                ->getSpool()
                ->flushQueue($app['swiftmailer.transport']);

            if (!$result) {
                return 'Some shit went wrong';
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
