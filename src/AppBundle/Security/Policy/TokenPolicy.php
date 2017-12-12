<?php

namespace AppBundle\Security\Policy;

use AppBundle\Exception\SecurityTokenPolicyException;

final class TokenPolicy
{
    private $regenerateToken;
    private $tokens;

    /**
     * @param bool    $regenerateToken
     * @param array $tokens
     */
    public function __construct($regenerateToken, array $tokens)
    {
        $this->regenerateToken = $regenerateToken;
        $this->tokens = $tokens;
    }

    public function shouldRegenerateToken()
    {
        return $this->regenerateToken;
    }

    public function validate($tokenToValidate)
    {
        if (0 === count($this->tokens)) {
            return null;
        }

        foreach ($this->tokens as $token) {
            if ($token === $tokenToValidate) {
                return $token;
            }
        }

        throw new SecurityTokenPolicyException();
    }
}
