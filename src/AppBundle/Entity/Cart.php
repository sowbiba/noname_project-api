<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Cart
 *
 * @ORM\Table(name="cart")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\CartRepository")
 *
 * @Serializer\AccessorOrder("custom", custom = {"id", "user", "cartDetails", "createdAt", "updatedAt"})
 * @Serializer\ExclusionPolicy("all")
 */
class Cart
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var User
     *
     * @ORM\OneToOne(targetEntity="User", inversedBy="cart")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=false, onDelete="CASCADE")
     *
     * @Serializer\Expose
     * @Serializer\Groups({
     *      "carts_list",
     *      "carts_create",
     *      "carts_read",
     *      "carts_update",
     * })
     */
    private $user;

    /**
     * @var \DateTime
     *
     * @Gedmo\Timestampable(on="create")
     *
     * @ORM\Column(name="created_at", type="datetime")
     *
     * @Serializer\Expose
     * @Serializer\Groups({
     *      "carts_list",
     *      "carts_create",
     *      "carts_read",
     *      "carts_update",
     * })
     */
    private $createdAt;

    /**
     * @var \DateTime
     *
     * @Gedmo\Timestampable(on="update")
     *
     * @ORM\Column(name="updated_at", type="datetime")
     *
     * @Serializer\Expose
     * @Serializer\Groups({
     *      "carts_list",
     *      "carts_create",
     *      "carts_read",
     *      "carts_update",
     * })
     */
    private $updatedAt;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="CartDetail", mappedBy="cart")
     *
     * @Serializer\Expose
     * @Serializer\Groups({
     *      "carts_create",
     *      "carts_read",
     *      "carts_update",
     * })
     */
    private $cartDetails;


    public function __construct()
    {
        $this->cartDetails = new ArrayCollection();
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
     * @param User $user
     *
     * @return Cart
     */
    public function setUser(User $user)
    {
        $this->user = $user;

        return $this;
    }
    /**
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return Cart
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
     * @return Cart
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
     * @return ArrayCollection|CartDetail[]
     */
    public function getCartDetails()
    {
        return $this->cartDetails;
    }
}

