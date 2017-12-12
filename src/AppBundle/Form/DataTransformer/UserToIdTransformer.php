<?php

namespace AppBundle\Form\DataTransformer;

use AppBundle\Entity\User;
use AppBundle\Manager\UserManager;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class UserToIdTransformer implements DataTransformerInterface
{
    /**
     * @var UserManager
     */
    private $userManager;

    /**
     * @param UserManager $userManager
     */
    public function __construct(UserManager $userManager)
    {
        $this->userManager = $userManager;
    }

    /**
     * Transforms an object (user) to an integer (id).
     *
     * @param User $user
     *
     * @return int|null
     */
    public function transform($user)
    {
        if (! ($user instanceof User)) {
            return;
        }

        return $user->getId();
    }

    /**
     * Transforms a integer (id) to an object (user).
     *
     * @param int|string $id
     *
     * @return User|null
     *
     * @throws TransformationFailedException if object (user) is not found
     */
    public function reverseTransform($id)
    {
        if (! is_numeric($id)) {
            return [];
        }

        $user = $this->userManager->find((int) $id);

        if (null === $user) {
            throw new TransformationFailedException(
                sprintf(
                    'User with ID "%s" does not exist!',
                    $id
                )
            );
        }

        return $user;
    }
}
