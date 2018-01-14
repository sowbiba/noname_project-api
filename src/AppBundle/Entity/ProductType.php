<?php

namespace AppBundle\Entity;

use AppBundle\Model\FilterableInterface;
use AppBundle\Model\PaginatableInterface;
use AppBundle\Model\SortableInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;

/**
 * ProductType
 *
 * @ORM\Table(name="product_type")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ProductTypeRepository")
 *
 * @Serializer\AccessorOrder("custom", custom = {"id", "name"})
 * @Serializer\ExclusionPolicy("all")
 *
 */
class ProductType implements FilterableInterface, SortableInterface, PaginatableInterface
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
     *      "product_types_list",
     *      "product_types_create",
     *      "product_types_read",
     *      "product_types_update",
     *      "products_list",
     *      "products_read",
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
     *      "product_types_list",
     *      "product_types_create",
     *      "product_types_read",
     *      "product_types_update",
     *      "products_list",
     *      "products_read",
     * })
     */
    private $name;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Product", mappedBy="productType")
     */
    private $products;

    public function __construct()
    {
        $this->products = new ArrayCollection();
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
     * @return ProductType
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
     * @return ArrayCollection|Product[]
     */
    public function getProducts()
    {
        return $this->products;
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
        return [
            'id' => ['field' => 'id'],
        ];
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
        return [
            'id' => ['field' => 'id'],
            'name' => ['field' => 'name'],
        ];
    }
}

