<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;

/**
 * DeliveryType
 *
 * @ORM\Table(name="delivery_type")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\DeliveryTypeRepository")
 *
 * @Serializer\AccessorOrder("custom", custom = {"id", "name", "delay", "price"})
 * @Serializer\ExclusionPolicy("all")
 */
class DeliveryType
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     *
     * @Serializer\Expose
     * @Serializer\Groups({
     *      "delivery_types_list",
     *      "delivery_types_create",
     *      "delivery_types_read",
     *      "delivery_types_update",
     *      "commands_list",
     *      "commands_create",
     *      "commands_read",
     *      "commands_update",
     * })
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255, unique=true)
     *
     * @Serializer\Expose
     * @Serializer\Groups({
     *      "delivery_types_list",
     *      "delivery_types_create",
     *      "delivery_types_read",
     *      "delivery_types_update",
     *      "commands_list",
     *      "commands_create",
     *      "commands_read",
     *      "commands_update",
     * })
     */
    private $name;

    /**
     * @var int
     *
     * @ORM\Column(name="delay", type="integer")
     *
     * @Serializer\Expose
     * @Serializer\Groups({
     *      "delivery_types_list",
     *      "delivery_types_create",
     *      "delivery_types_read",
     *      "delivery_types_update",
     * })
     */
    private $delay;

    /**
     * @var float
     *
     * @ORM\Column(name="price", type="decimal", precision=20, scale=9)
     *
     * @Serializer\Expose
     * @Serializer\Groups({
     *      "delivery_types_list",
     *      "delivery_types_create",
     *      "delivery_types_read",
     *      "delivery_types_update",
     * })
     */
    private $price;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Command", mappedBy="deliveryType")
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
     * Set name
     *
     * @param string $name
     *
     * @return DeliveryType
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set delay
     *
     * @param integer $delay
     *
     * @return DeliveryType
     */
    public function setDelay($delay)
    {
        $this->delay = $delay;

        return $this;
    }

    /**
     * Get delay
     *
     * @return int
     */
    public function getDelay()
    {
        return $this->delay;
    }

    /**
     * Set price
     *
     * @param float $price
     *
     * @return Product
     */
    public function setPrice($price)
    {
        $this->price = $price;

        return $this;
    }

    /**
     * Get price
     *
     * @return float
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * @return ArrayCollection|Command[]
     */
    public function getCommands()
    {
        return $this->commands;
    }
}

