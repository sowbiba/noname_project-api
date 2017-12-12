<?php

namespace AppBundle\Security;

use Symfony\Component\Security\Core\Encoder\MessageDigestPasswordEncoder;

final class PasswordEncoder extends MessageDigestPasswordEncoder
{
    public function __construct()
    {
        parent::__construct('sha1', false, 1);
    }

    protected function mergePasswordAndSalt($password, $salt)
    {
        return $salt.strtolower($password);
    }
}
