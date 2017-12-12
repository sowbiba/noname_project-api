<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Command
 *
 * @ORM\Table(name="command")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\CommandRepository")
 *
 * @Serializer\AccessorOrder("custom", custom = {"id", "user", "orderedAt", "total", "deliveryType", "deliveryStatus", "factureFile", "commandDetails","createdAt", "createdBy", "updatedAt", "updatedBy"})
 * @Serializer\ExclusionPolicy("all")
 */
class Command
{
    const DELIVERY_STATUS_WAITING       = 'WAITING';
    const DELIVERY_STATUS_IN_PROGRESS   = 'IN_PROGRESS';
    const DELIVERY_STATUS_DONE          = 'DONE';

    static $allowedDeliveryStatus = array(
        self::DELIVERY_STATUS_WAITING,
        self::DELIVERY_STATUS_IN_PROGRESS,
        self::DELIVERY_STATUS_DONE,
    );

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     *
     * @Serializer\Expose
     * @Serializer\Groups({
     *      "commands_list",
     *      "commands_create",
     *      "commands_read",
     *      "commands_update",
     * })
     */
    private $id;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="User", inversedBy="commands")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     *
     * @Serializer\Expose
     * @Serializer\Groups({
     *      "commands_list",
     *      "commands_create",
     *      "commands_read",
     *      "commands_update",
     * })
     */
    private $user;

    /**
     * @var \DateTime
     *
     * @Gedmo\Timestampable(on="create")
     *
     * @ORM\Column(name="ordered_at", type="datetime")
     *
     * @Serializer\Expose
     * @Serializer\Groups({
     *      "commands_list",
     *      "commands_create",
     *      "commands_read",
     *      "commands_update",
     * })
     */
    private $orderedAt;

    /**
     * @var float
     *
     * @ORM\Column(name="total", type="decimal", precision=20, scale=9)
     *
     * @Serializer\Expose
     * @Serializer\Groups({
     *      "commands_list",
     *      "commands_create",
     *      "commands_read",
     *      "commands_update",
     * })
     */
    private $total;

    /**
     * @var DeliveryType
     *
     * @ORM\ManyToOne(targetEntity="DeliveryType", inversedBy="commands")
     * @ORM\JoinColumn(name="delivery_type_id", referencedColumnName="id")
     *
     * @Serializer\Expose
     * @Serializer\Groups({
     *      "commands_list",
     *      "commands_create",
     *      "commands_read",
     *      "commands_update",
     * })
     */
    private $deliveryType;

    /**
     * @var string
     *
     * @ORM\Column(name="delivery_status", type="string", length=255)
     *
     * @Serializer\Expose
     * @Serializer\Groups({
     *      "commands_list",
     *      "commands_create",
     *      "commands_read",
     *      "commands_update",
     * })
     */
    private $deliveryStatus;

    /**
     * @var string
     *
     * @ORM\Column(name="facture_file", type="string", length=255)
     *
     * @Serializer\Expose
     * @Serializer\Groups({
     *      "commands_create",
     *      "commands_read",
     *      "commands_update",
     * })
     */
    private $factureFile;

    /**
     * @var \DateTime
     *
     * @Gedmo\Timestampable(on="create")
     *
     * @ORM\Column(name="created_at", type="datetime")
     *
     * @Serializer\Expose
     * @Serializer\Groups({
     *      "commands_list",
     *      "commands_create",
     *      "commands_read",
     *      "commands_update",
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
     *      "commands_list",
     *      "commands_create",
     *      "commands_read",
     *      "commands_update",
     * })
     */
    private $updatedAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="delivered_at", type="datetime", nullable=true)
     *
     * @Serializer\Expose
     * @Serializer\Groups({
     *      "commands_list",
     *      "commands_create",
     *      "commands_read",
     *      "commands_update",
     * })
     */
    private $deliveredAt;

    /**
     * @var User
     *
     * @Gedmo\Blameable(on="create")
     *
     * @ORM\Column(name="created_by", type="string", nullable=false)
     *
     * @Serializer\Expose
     * @Serializer\Groups({
     *      "commands_list",
     *      "commands_create",
     *      "commands_read",
     *      "commands_update",
     * })
     */
    private $createdBy;

    /**
     * @var User
     *
     * @ORM\Column(name="updated_by", type="string", nullable=true)
     *
     * @Gedmo\Blameable(on="change", field={"total", "deliveryType", "deliveryStatus", "factureFile", "commandDetails"})
     *
     * @Serializer\Expose
     * @Serializer\Groups({
     *      "commands_list",
     *      "commands_create",
     *      "commands_read",
     *      "commands_update",
     * })
     */
    private $updatedBy;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="CommandDetail", mappedBy="command", cascade={"persist"})
     *
     * @Serializer\Expose
     * @Serializer\Groups({
     *      "commands_create",
     *      "commands_read",
     *      "commands_update",
     * })
     */
    private $commandDetails;


    public function __construct()
    {
        $this->setDeliveryStatus(self::DELIVERY_STATUS_WAITING);
        $this->commandDetails = new ArrayCollection();
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
     * Set user
     *
     * @param User $user
     *
     * @return Command
     */
    public function setUser(User $user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set orderedAt
     *
     * @param \DateTime $orderedAt
     *
     * @return Command
     */
    public function setOrderedAt($orderedAt)
    {
        $this->orderedAt = $orderedAt;

        return $this;
    }

    /**
     * Get orderedAt
     *
     * @return \DateTime
     */
    public function getOrderedAt()
    {
        return $this->orderedAt;
    }

    /**
     * Set total
     *
     * @param float $total
     *
     * @return Command
     */
    public function setTotal($total)
    {
        $this->total = $total;

        return $this;
    }

    /**
     * Get total
     *
     * @return float
     */
    public function getTotal()
    {
        return $this->total;
    }

    /**
     * Set deliveryType
     *
     * @param DeliveryType $deliveryType
     *
     * @return Command
     */
    public function setDeliveryType(DeliveryType $deliveryType)
    {
        $this->deliveryType = $deliveryType;

        return $this;
    }

    /**
     * Get deliveryType
     *
     * @return DeliveryType
     */
    public function getDeliveryType()
    {
        return $this->deliveryType;
    }

    /**
     * Set deliveryStatus
     *
     * @param string $deliveryStatus
     *
     * @return Command
     */
    public function setDeliveryStatus($deliveryStatus)
    {
        $this->deliveryStatus = $deliveryStatus;

        return $this;
    }

    /**
     * Get deliveryStatus
     *
     * @return string
     */
    public function getDeliveryStatus()
    {
        return $this->deliveryStatus;
    }

    /**
     * Set factureFile
     *
     * @param string $factureFile
     *
     * @return Command
     */
    public function setFactureFile($factureFile)
    {
        $this->factureFile = $factureFile;

        return $this;
    }

    /**
     * Get factureFile
     *
     * @return string
     */
    public function getFactureFile()
    {
        return $this->factureFile;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return Command
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
     * @return Command
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
     * Set deliveredAt
     *
     * @param \DateTime $deliveredAt
     *
     * @return Command
     */
    public function setDeliveredAt($deliveredAt)
    {
        $this->deliveredAt = $deliveredAt;

        return $this;
    }

    /**
     * Get deliveredAt
     *
     * @return \DateTime
     */
    public function getDeliveredAt()
    {
        return $this->deliveredAt;
    }

    /**
     * Set createdBy
     *
     * @param int $createdBy
     *
     * @return Command
     */
    public function setCreatedBy($createdBy)
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    /**
     * Get createdBy
     *
     * @return int
     */
    public function getCreatedBy()
    {
        return $this->createdBy;
    }

    /**
     * Set updatedBy
     *
     * @param int $updatedBy
     *
     * @return Command
     */
    public function setUpdatedBy($updatedBy)
    {
        $this->updatedBy = $updatedBy;

        return $this;
    }

    /**
     * Get updatedBy
     *
     * @return int
     */
    public function getUpdatedBy()
    {
        return $this->updatedBy;
    }

    /**
     * @return ArrayCollection|CommandDetail[]
     */
    public function getCommandDetails()
    {
        return $this->commandDetails;
    }

    /**
     * @param CommandDetail $commandDetail
     *
     * @return Command
     */
    public function addCommandDetail(CommandDetail $commandDetail)
    {
        if (!$this->commandDetails->contains($commandDetail)) {
            $this->commandDetails->add($commandDetail);
        }

        return $this;
    }
}

