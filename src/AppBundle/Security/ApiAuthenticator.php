<?php
/**
 * Created by PhpStorm.
 * User: isow
 * Date: 27/11/17
 * Time: 19:29
 */

namespace AppBundle\Security;

use AppBundle\Security\UserProvider\ApiUserProvider;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Http\Authentication\SimpleFormAuthenticatorInterface;

final class ApiAuthenticator implements SimpleFormAuthenticatorInterface
{
    public function createToken(Request $request, $username, $password, $providerKey)
    {
        return new UsernamePasswordToken($username, $password, $providerKey);
    }

    public function authenticateToken(TokenInterface $token, UserProviderInterface $userProvider, $providerKey)
    {
        if (!$userProvider instanceof ApiUserProvider) {
            throw new \InvalidArgumentException(
                sprintf('Instances of "%s" are not supported.', get_class($userProvider))
            );
        }

        $user = $userProvider->loadUserByUsernameAndPassword($token->getUsername(), $token->getCredentials());

        return new UsernamePasswordToken($user, $token->getCredentials(), $providerKey, $user->getRolesNames());
    }

    public function supportsToken(TokenInterface $token, $providerKey)
    {
        return $token instanceof UsernamePasswordToken && $providerKey === $token->getProviderKey();
    }
}