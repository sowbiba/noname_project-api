<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * Product
 *
 * @ORM\Table(name="product")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ProductRepository")
 *
 * @Serializer\AccessorOrder("custom", custom = {"id", "name", "productType", "price", "photoFile"})
 * @Serializer\ExclusionPolicy("all")
 */
class Product
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
     *      "products_list",
     *      "products_create",
     *      "products_read",
     *      "products_update",
     *      "carts_read",
     *      "carts_update",
     * })
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     *
     * @Serializer\Expose
     * @Serializer\Groups({
     *      "products_list",
     *      "products_create",
     *      "products_read",
     *      "products_update",
     *      "carts_read",
     *      "carts_update",
     * })
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", nullable=false)
     *
     * @Serializer\Expose
     * @Serializer\Groups({
     *      "products_list",
     *      "products_create",
     *      "products_read",
     *      "products_update",
     * })
     */
    private $description;

    /**
     * @var float
     *
     * @ORM\Column(name="price", type="decimal", precision=20, scale=9, nullable=false)
     *
     * @Serializer\Expose
     * @Serializer\Groups({
     *      "products_list",
     *      "products_create",
     *      "products_read",
     *      "products_update",
     *      "carts_read",
     *      "carts_update",
     * })
     */
    private $price;

    /**
     * @var ProductType
     *
     * @ORM\ManyToOne(targetEntity="ProductType", inversedBy="products")
     * @ORM\JoinColumn(name="product_type_id", referencedColumnName="id", nullable=false)
     *
     * @Serializer\Expose
     * @Serializer\Groups({
     *      "products_list",
     *      "products_create",
     *      "products_read",
     *      "products_update",
     * })
     */
    private $productType;

    /**
     * @var string
     *
     * @ORM\Column(name="photo_file", type="string", length=255, nullable=true)
     *
     * @Serializer\Expose
     * @Serializer\Groups({
     *      "products_list",
     *      "products_create",
     *      "products_read",
     *      "products_update",
     * })
     */
    private $photoFile;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_at", type="datetime")
     *
     * @Gedmo\Timestampable(on="create")
     *
     * @Serializer\Expose
     * @Serializer\Groups({
     *      "products_list",
     *      "products_read",
     * })
     */
    private $createdAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updated_at", type="datetime")
     *
     * @Gedmo\Timestampable(on="update")
     *
     * @Serializer\Expose
     * @Serializer\Groups({
     *      "products_list",
     *      "products_read",
     * })
     */
    private $updatedAt;

    /**
     * @var User
     *
     * @ORM\Column(name="created_by", type="string", nullable=false)
     *
     * @Gedmo\Blameable(on="create")
     *
     * @Serializer\Expose
     * @Serializer\Groups({
     *      "products_list",
     *      "products_read",
     * })
     */
    private $createdBy;

    /**
     * @var User
     *
     * @ORM\Column(name="updated_by", type="string", nullable=true)
     *
     * @Gedmo\Blameable(on="change", field={"name", "price", "photoFile", "productType"})
     *
     * @Serializer\Expose
     * @Serializer\Groups({
     *      "products_list",
     *      "products_read",
     * })
     */
    private $updatedBy;

    /**
     * @var Stock
     *
     * @ORM\OneToOne(targetEntity="Stock", mappedBy="product", cascade={"persist", "remove"})
     */
    protected $stock;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="CommandDetail", mappedBy="product")
     */
    private $commandDetails;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="CartDetail", mappedBy="product")
     */
    private $cartDetails;


    public function __construct()
    {
        $this->commandDetails = new ArrayCollection();
        $this->cartDetails = new ArrayCollection();

        $this->stock = new Stock($this);
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
     * @return Product
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
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
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
     * Set productType
     *
     * @param ProductType $productType
     *
     * @return Product
     */
    public function setProductType(ProductType $productType)
    {
        $this->productType = $productType;

        return $this;
    }

    /**
     * Get productType
     *
     * @return ProductType
     */
    public function getProductType()
    {
        return $this->productType;
    }

    /**
     * Set photoFile
     *
     * @param string $photoFile
     *
     * @return Product
     */
    public function setPhotoFile($photoFile)
    {
        $this->photoFile = $photoFile;

        return $this;
    }

    /**
     * Get photoFile
     *
     * @return string
     */
    public function getPhotoFile()
    {
        return $this->photoFile;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return Product
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
     * @return Product
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
     * Set createdBy
     *
     * @param int $createdBy
     *
     * @return Product
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
     * @return Product
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
     * @return Stock
     */
    public function getStock()
    {
        return $this->stock;
    }

    /**
     * @return ArrayCollection|CommandDetail[]
     */
    public function getCommandDetails()
    {
        return $this->commandDetails;
    }

    /**
     * @return ArrayCollection|CartDetail[]
     */
    public function getCartDetails()
    {
        return $this->cartDetails;
    }
}

