<?php

namespace AppBundle\Security;

use AppBundle\Entity\Role as ApiRole;
use AppBundle\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Role\Role;
use Symfony\Component\Security\Core\Role\RoleHierarchy;
use Symfony\Component\Security\Core\Role\RoleInterface;

final class ApiVoter extends Voter
{
    const CREATE = 'create';
    const VIEW = 'view';
    const EDIT = 'edit';
    const DELETE = 'delete';

    /**
     * @var array
     */
    public $rolesHierarchy;

    public function __construct(array $rolesHierarchy = array())
    {
        $this->rolesHierarchy = $rolesHierarchy;
    }

    protected function supports($attribute, $subject)
    {
        return true; // We have only one voter which validates all the requests.
    }

    protected function voteOnAttribute($attribute, $subject, TokenInterface $token)
    {
        $user = $token->getUser();
        if (!$user instanceof User) {
            return false;
        }

        switch ($attribute) {
            case static::CREATE:
                return $this->canCreate($user);
            case static::VIEW:
                return $this->canView();
            case static::EDIT:
                return $this->canEdit($user);
            case static::DELETE:
                return $this->canDelete($user);
        }

        return true; // As long as the user is logged, the user can access not configured routes.
    }

    private function canCreate(User $user)
    {
        return in_array('ADMIN', $this->getUserReachableRoles($user), true);
    }

    private function canView()
    {
        return true; // Everyone can access the "view" services as long as they are logged.
    }

    private function canEdit(User $user)
    {
        return in_array('ADMIN', $this->getUserReachableRoles($user), true);
    }

    private function canDelete(User $user)
    {
        return in_array('ADMIN', $this->getUserReachableRoles($user), true);
    }

    private function getUserReachableRoles(User $user)
    {
        $roleHierarchy = new RoleHierarchy($this->rolesHierarchy);

        return array_map(function (RoleInterface $role) {
            return $role->getRole();
        }, $roleHierarchy->getReachableRoles($user->getRoles()));
    }
}
