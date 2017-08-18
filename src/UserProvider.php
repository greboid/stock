<?php
    declare(strict_types=1);

    namespace greboid\stock;

    use Symfony\Component\Security\Core\User\UserProviderInterface;
    use Symfony\Component\Security\Core\User\UserInterface;
    use Symfony\Component\Security\Core\User\User;
    use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
    use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;

    class UserProvider implements UserProviderInterface
    {
        private $conn;

        public function __construct(\PDO $conn)
        {
            $this->conn = $conn;
        }

        public function loadUserByUsername($username)
        {
            $statement = $this->conn->prepare('
                SELECT * FROM accounts WHERE username=:username
            ');
            $statement->bindValue(':username', strtolower($username), \PDO::PARAM_STR);
            $statement->execute();
            $results = $statement->fetchObject();

            if ($results === false) {
                throw new UsernameNotFoundException(sprintf('Username "%s" does not exist.', $username));
            }

            //return new User($results->username, $results->password, explode(',', $results->roles), true, true, true, true);
            return new User($results->username, $results->password, array(), true, true, true, true);
        }

        public function refreshUser(UserInterface $user)
        {
            if (!$user instanceof User) {
                throw new UnsupportedUserException(sprintf('Instances of "%s" are not supported.', get_class($user)));
            }

            return $this->loadUserByUsername($user->getUsername());
        }

        public function supportsClass($class)
        {
            return $class === 'Symfony\Component\Security\Core\User\User';
        }
    }
