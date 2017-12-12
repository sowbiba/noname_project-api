<?php

namespace AppBundle\Entity;

use AppBundle\Model\FilterableInterface;
use AppBundle\Model\PaginatableInterface;
use AppBundle\Model\SortableInterface;
use AppBundle\ParamConverter\CollectionParamConverter;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Intl\Exception\NotImplementedException;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * User
 *
 * @ORM\Table(name="user")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\UserRepository")
 *
 * @Serializer\AccessorOrder("custom", custom = {"id", "username", "role", "firstname", "lastname", "email", "phone", "address"})
 * @Serializer\ExclusionPolicy("all")
 */
class User implements UserInterface, FilterableInterface, SortableInterface, PaginatableInterface
{
    const DEFAULT_PAGINATION_NUM_ITEMS = PaginatableInterface::DEFAULT_NUM_ITEMS;

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     *
     * @Serializer\Expose
     * @Serializer\Groups({
     *      "users_list",
     *      "users_create",
     *      "users_read",
     *      "users_update",
     *      "authentication",
     *      "commands_list",
     *      "commands_create",
     *      "commands_read",
     *      "commands_update",
     *      "carts_list",
     *      "carts_create",
     *      "carts_read",
     *      "carts_update",
     * })
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="firstname", type="string", length=255)
     *
     * @Serializer\Expose
     * @Serializer\Groups({
     *      "users_list",
     *      "users_create",
     *      "users_read",
     *      "users_update",
     *      "authentication",
     *      "commands_list",
     *      "commands_create",
     *      "commands_read",
     *      "commands_update",
     *      "carts_list",
     *      "carts_create",
     *      "carts_read",
     *      "carts_update",
     * })
     */
    private $firstname;

    /**
     * @var string
     *
     * @ORM\Column(name="lastname", type="string", length=255)
     *
     * @Serializer\Expose
     * @Serializer\Groups({
     *      "users_list",
     *      "users_create",
     *      "users_read",
     *      "users_update",
     *      "authentication",
     *      "commands_list",
     *      "commands_create",
     *      "commands_read",
     *      "commands_update",
     *      "carts_list",
     *      "carts_create",
     *      "carts_read",
     *      "carts_update",
     * })
     */
    private $lastname;

    /**
     * @var string
     *
     * @ORM\Column(name="phone", type="string", length=255)
     *
     * @Serializer\Expose
     * @Serializer\Groups({
     *      "users_list",
     *      "users_create",
     *      "users_read",
     *      "users_update",
     * })
     */
    private $phone;

    /**
     * @var string
     *
     * @ORM\Column(name="address", type="text")
     *
     * @Serializer\Expose
     * @Serializer\Groups({
     *      "users_create",
     *      "users_read",
     *      "users_update",
     * })
     */
    private $address;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="birthdate", type="datetime", nullable=true)
     *
     * @Serializer\Expose
     * @Serializer\Groups({
     *      "users_create",
     *      "users_read",
     *      "users_update",
     * })
     */
    private $birthdate;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=255, unique=true)
     *
     * @Serializer\Expose
     * @Serializer\Groups({
     *      "users_list",
     *      "users_create",
     *      "users_read",
     *      "users_update",
     *      "authentication",
     * })
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(name="username", type="string", length=255, unique=true)
     *
     * @Serializer\Expose
     * @Serializer\Groups({
     *      "users_list",
     *      "users_create",
     *      "users_read",
     *      "users_update",
     *      "authentication",
     * })
     */
    private $username;

    /**
     * @var string
     *
     * @ORM\Column(name="password", type="string", length=255)
     */
    private $password;

    /**
     * @var string
     *
     * @ORM\Column(name="token", type="string", length=255, nullable=true)
     *
     * @Serializer\Expose
     * @Serializer\Groups({
     *      "authentication",
     * })
     */
    private $token;

    /**
     * @var bool
     *
     * @ORM\Column(name="active", type="boolean")
     *
     * @Serializer\Expose
     * @Serializer\Groups({
     *      "users_list",
     *      "users_create",
     *      "users_read",
     *      "users_update",
     * })
     */
    private $active;

    /**
     * @var \DateTime
     *
     * @Gedmo\Timestampable(on="create")
     *
     * @ORM\Column(name="created_at", type="datetime")
     */
    private $createdAt;

    /**
     * @var \DateTime
     *
     * @Gedmo\Timestampable(on="update")
     *
     * @ORM\Column(name="updated_at", type="datetime")
     */
    private $updatedAt;

    /**
     * @var Role
     *
     * @ORM\ManyToOne(targetEntity="Role", inversedBy="users")
     * @ORM\JoinColumn(name="role_id", referencedColumnName="id", nullable=false)
     *
     * @Serializer\Expose
     * @Serializer\Groups({
     *      "users_list",
     *      "users_create",
     *      "users_read",
     *      "users_update",
     *      "authentication",
     * })
     */
    private $role;

    /**
     * @var Cart
     *
     * @ORM\OneToOne(targetEntity="Cart", mappedBy="user", cascade={"persist", "remove"})
     */
    protected $cart;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Command", mappedBy="user")
     */
    private $commands;



    public function __construct()
    {
        $this->commands = new ArrayCollection();
    }

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set firstname
     *
     * @param string $firstname
     *
     * @return User
     */
    public function setFirstname($firstname)
    {
        $this->firstname = $firstname;

        return $this;
    }

    /**
     * Get firstname
     *
     * @return string
     */
    public function getFirstname()
    {
        return $this->firstname;
    }

    /**
     * Set lastname
     *
     * @param string $lastname
     *
     * @return User
     */
    public function setLastname($lastname)
    {
        $this->lastname = $lastname;

        return $this;
    }

    /**
     * Get lastname
     *
     * @return string
     */
    public function getLastname()
    {
        return $this->lastname;
    }

    /**
     * Set phone
     *
     * @param string $phone
     *
     * @return User
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * Get phone
     *
     * @return string
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * Set address
     *
     * @param string $address
     *
     * @return User
     */
    public function setAddress($address)
    {
        $this->address = $address;

        return $this;
    }

    /**
     * Get address
     *
     * @return string
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * Set birthdate
     *
     * @param \DateTime $birthdate
     *
     * @return User
     */
    public function setBirthdate($birthdate)
    {
        $this->birthdate = $birthdate;

        return $this;
    }

    /**
     * Get birthdate
     *
     * @return \DateTime
     */
    public function getBirthdate()
    {
        return $this->birthdate;
    }

    /**
     * Set email
     *
     * @param string $email
     *
     * @return User
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set username
     *
     * @param string $username
     *
     * @return User
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * Get username
     *
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Set password
     *
     * @param string $password
     *
     * @return User
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Get password
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @return string
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * @param string $token
     */
    public function setToken($token)
    {
        $this->token = $token;
    }

    /**
     * @return boolean
     */
    public function isActive()
    {
        return $this->active;
    }

    /**
     * @param boolean $active
     */
    public function setActive($active)
    {
        $this->active = $active;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return User
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set updatedAt
     *
     * @param \DateTime $updatedAt
     *
     * @return User
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    /**
     * Get updatedAt
     *
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * Set role
     *
     * @param Role $role
     *
     * @return User
     */
    public function setRole(Role $role)
    {
        $this->role = $role;

        return $this;
    }

    /**
     * Get role
     *
     * @return Role
     */
    public function getRole()
    {
        return $this->role;
    }

    /**
     * @return Cart
     */
    public function getCart()
    {
        return $this->cart;
    }

    /**
     * @return ArrayCollection|Command[]
     */
    public function getCommands()
    {
        return $this->commands;
    }

    public function getSalt()
    {
        return '';
    }

    public function eraseCredentials()
    {
        //throw new NotImplementedException("Yo à implementer");
    }

    public function getRoles()
    {
        return array($this->getRole());
    }

    public function getRolesNames()
    {
        return array($this->getRole()->getName());
    }

    public function __toString()
    {
        return $this->getFirstname() . " " . $this->getLastname();
    }

    /**
     * @return array
     */
    public static function getDefaultSortOrder()
    {
        return [
            'id' => SortableInterface::SORT_ORDER_ASC,
        ];
    }

    /**
     * @return array
     */
    public static function getOrdersMapping()
    {
        return [];
    }

    /**
     * @return int
     */
    public function getDefaultPaginationNumItems()
    {
        return static::DEFAULT_PAGINATION_NUM_ITEMS;
    }

    /**
     * @return array
     */
    public static function getFiltersMapping()
    {
        return [];
    }

    public function getAlgorithm()
    {
        return 'sha1';
    }
}

