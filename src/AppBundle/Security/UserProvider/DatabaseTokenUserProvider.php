<?php

namespace AppBundle\Security\UserProvider;

use AppBundle\Entity\User;
use AppBundle\Repository\UserRepository;
use Psr\Log\LoggerInterface;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\UserProviderInterface;

final class DatabaseTokenUserProvider implements UserProviderInterface
{
    private $userRepository;
    private $logger;

    public function __construct(UserRepository $userRepository, LoggerInterface $logger)
    {
        $this->userRepository = $userRepository;
        $this->logger = $logger;
    }

    public function loadUserByUsername($tokenToValidate)
    {
        if (null === $user = $this->userRepository->findValidUserByToken($tokenToValidate)) {
            $this->logger->error(
                "No valid user in database using the token `$tokenToValidate`",
                ['DatabaseTokenUserProvider']
            );

            throw new UsernameNotFoundException();
        }

        return $user;
    }

    public function refreshUser(UserInterface $user)
    {
        throw new UnsupportedUserException();
    }

    public function supportsClass($class)
    {
        return User::class === $class;
    }
}
