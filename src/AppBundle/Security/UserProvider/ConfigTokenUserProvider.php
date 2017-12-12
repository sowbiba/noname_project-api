<?php

namespace AppBundle\Security\UserProvider;

use AppBundle\Entity\Role;
use AppBundle\Entity\User;
use AppBundle\Exception\SecurityTokenPolicyException;
use AppBundle\Repository\RoleRepository;
use AppBundle\Security\Policy\TokenPolicy;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\User\UserProviderInterface;

final class ConfigTokenUserProvider implements UserProviderInterface
{
    private $tokenPolicy;
    private $requestStack;
    private $roleRepository;
    private $logger;

    public function __construct(
        TokenPolicy $tokenPolicy,
        RequestStack $requestStack,
        RoleRepository $roleRepository,
        LoggerInterface $logger
    ) {
        $this->tokenPolicy = $tokenPolicy;
        $this->requestStack = $requestStack;
        $this->roleRepository = $roleRepository;
        $this->logger = $logger;
    }

    public function loadUserByUsername($tokenToValidate)
    {
        if (null === $request = $this->requestStack->getCurrentRequest()) {
            throw new UsernameNotFoundException();
        }

        try {
            if (null === $token = $this->tokenPolicy->validate($tokenToValidate)) {
                throw new UsernameNotFoundException();
            }
        } catch (SecurityTokenPolicyException $e) {
            $this->logger->error(
                "No configured token matching `$tokenToValidate` in `{$request->getPathInfo()}` path",
                ['ConfigTokenUserProvider']
            );

            throw new UsernameNotFoundException();
        }

        $user = new User(false);
        $user->setRole($this->roleRepository->findOneBy(['name' => 'ADMIN']));
        $user->setUsername('guest');
        $user->setToken($token->getValue());

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
