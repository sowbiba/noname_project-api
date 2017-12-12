<?php
namespace AppBundle\Manager;


use AppBundle\Entity\User;
use AppBundle\Exception\NotValidPasswordException;
use AppBundle\Security\PasswordEncoder;
use AppBundle\Security\Policy\TokenPolicy;
use AppBundle\Utils\HashGenerator;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;

class UserManager extends CRUDManager {

    /**
     * @param string         $username
     * @param string         $password
     * @param TokenPolicy $tokenPolicy
     *
     * @throws NotValidPasswordException
     * @throws UsernameNotFoundException
     *
     * @return null|User
     */
    public function loadUserByCredentials($username, $password, TokenPolicy $tokenPolicy)
    {
        if (null === $username || null === $password) {
            throw new UsernameNotFoundException();
        }

        /** @var User $user */
        $user = $this->getRepository()->findValidUserByUsername($username);

        if (null !== $user) {
            $encoder = new PasswordEncoder();

            if ($encoder->isPasswordValid($user->getPassword(), $password, $user->getSalt())) {
                if ($tokenPolicy->shouldRegenerateToken()) {
                    // If the authentication is successful, we generate a new token for the user.
                    $this->generateToken($user);
                }
            } else {
                throw new NotValidPasswordException();
            }
        }

        return $user;
    }


    /**
     * Generate a new token for the user.
     * We use here a native SQL update query to avoid passing in a listener (such as Gedmo\Timestampable).
     *
     * @param User $user
     *
     * @throws \Doctrine\DBAL\DBALException
     */
    public function generateToken(User $user)
    {
        $entityManager = $this->getEntityManager();
        $token = HashGenerator::generate();
        $user->setToken($token);
        $entityManager->getConnection()->executeUpdate("
            UPDATE {$entityManager->getClassMetadata($this->entityClass)->getTableName()}
            SET token = ?
            WHERE id = ?",
            [$token, $user->getId()]
        );
    }
} 