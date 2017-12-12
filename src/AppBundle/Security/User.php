<?php
/**
 * Created by PhpStorm.
 * User: isow
 * Date: 27/11/17
 * Time: 19:20
 */

namespace AppBundle\Security;

use Symfony\Component\Security\Core\User\EquatableInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class User implements UserInterface, EquatableInterface
{
    public function __construct(array $data = [])
    {
        foreach ($data as $property => $value) {
            $setMethodName = 'set'.ucfirst($property);

            if (method_exists($this, $setMethodName)) {
                $this->{$setMethodName}($value);
            }
        }
    }

    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $username;

    /**
     * @var string
     */
    private $email;

    /**
     * @var string
     */
    private $firstname;

    /**
     * @var string
     */
    private $name;

    /**
     * @var array
     */
    private $roles = [];

    /**
     * @var string
     */
    private $token;

    public static function fromApiResponse(array $data)
    {
        return new self([
            'email' => isset($data['email']) ? $data['email'] : null,
            'firstname' => isset($data['firstname']) ? $data['firstname'] : null,
            'id' => $data['id'],
            'name' => isset($data['lastname']) ? $data['lastname'] : null,
            'roles' => isset($data['role']) ? array($data['role']) : null,
            'token' => $data['token'],
            'username' => $data['username'],
        ]);
    }

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function setUsername($username)
    {
        $this->username = $username;
    }

    public function getUsername()
    {
        return $this->username;
    }

    public function setEmail($email)
    {
        $this->email = $email;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function setFirstname($firstname)
    {
        $this->firstname = $firstname;
    }

    public function getFirstname()
    {
        return $this->firstname;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setToken($token)
    {
        $this->token = $token;
    }

    public function getToken()
    {
        return $this->token;
    }

    public function setRoles($roles)
    {
        $this->roles = $roles;
    }

    public function getRoles()
    {
        return $this->roles;
    }

    public function getRolesNames()
    {
        $roles = array();

        foreach ($this->getRoles() as $role) {
            $roleName = strtoupper($role['name']);
            if (!strpos($roleName, 'ROLE_')) {
                array_push($roles, 'ROLE_'.$roleName);
            } else {
                array_push($roles, $roleName);
            }
        }

        return $roles;
    }

    /**
     * Returns the salt that was originally used to encode the password.
     *
     * This can return null if the password was not encoded using a salt.
     *
     * @return string|null The salt
     */
    public function getSalt()
    {
        // TODO: Implement getSalt() method.
    }

    /**
     * Returns the password used to authenticate the user.
     *
     * This should be the encoded password. On authentication, a plain-text
     * password will be salted, encoded, and then compared to this value.
     *
     * @return string The password
     */
    public function getPassword()
    {
        // TODO: Implement getPassword() method.
    }

    /**
     * Removes sensitive data from the user.
     *
     * This is important if, at any given point, sensitive information like
     * the plain-text password is stored on this object.
     */
    public function eraseCredentials()
    {
        // TODO: Implement eraseCredentials() method.
    }

    /**
     * {@inheritdoc}
     *
     * Two users are "equals" if they have the same ID or Profideo ID, and the same roles.
     */
    public function isEqualTo(UserInterface $user)
    {
        if ($user instanceof self) {
            if ($this->id !== $user->getId()) {
                return false;
            }

            // Check that the roles are the same, in any order
            $isEqualRoles = count($this->getRoles()) == count($user->getRoles());

            if ($isEqualRoles) {
                foreach ($this->getRoles() as $role) {
                    $isEqualRoles = $isEqualRoles && in_array($role, $user->getRoles());
                }
            }

            if (!$isEqualRoles) {
                return false;
            }

            return true;
        }

        return false;
    }
}