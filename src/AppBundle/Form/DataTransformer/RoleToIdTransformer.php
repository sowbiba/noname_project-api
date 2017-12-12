<?php

namespace AppBundle\Form\DataTransformer;

use AppBundle\Entity\Role;
use AppBundle\Manager\RoleManager;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class RoleToIdTransformer implements DataTransformerInterface
{
    /**
     * @var RoleManager
     */
    private $roleManager;

    /**
     * @param RoleManager $roleManager
     */
    public function __construct(RoleManager $roleManager)
    {
        $this->roleManager = $roleManager;
    }

    /**
     * Transforms an object (role) to an integer (id).
     *
     * @param Role $role
     *
     * @return int|null
     */
    public function transform($role)
    {
        if (! ($role instanceof Role)) {
            return;
        }

        return $role->getId();
    }

    /**
     * Transforms a integer (id) to an object (role).
     *
     * @param int|string $id
     *
     * @return Role|null
     *
     * @throws TransformationFailedException if object (role) is not found
     */
    public function reverseTransform($id)
    {
        if (! is_numeric($id)) {
            return [];
        }

        $role = $this->roleManager->find((int) $id);

        if (null === $role) {
            throw new TransformationFailedException(
                sprintf(
                    'Role with ID "%s" does not exist!',
                    $id
                )
            );
        }

        return $role;
    }
}
