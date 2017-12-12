<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;

/**
 * CartDetail
 *
 * @ORM\Table(name="cart_detail")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\CartDetailRepository")
 *
 * @Serializer\AccessorOrder("custom", custom = {"product", "quantity"})
 * @Serializer\ExclusionPolicy("all")
 */
class CartDetail
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
     * @var Cart
     *
     * @ORM\ManyToOne(targetEntity="Cart", inversedBy="cartDetails")
     * @ORM\JoinColumn(name="cart_id", referencedColumnName="id", onDelete="CASCADE")
     */
    private $cart;

    /**
     * @var Product
     *
     * @ORM\ManyToOne(targetEntity="Product", inversedBy="cartDetails")
     * @ORM\JoinColumn(name="product_id", referencedColumnName="id")
     *
     * @Serializer\Expose
     * @Serializer\Groups({
     *      "carts_read",
     *      "carts_update",
     * })
     */
    private $product;

    /**
     * @var int
     *
     * @ORM\Column(name="quantity", type="integer")
     *
     * @Serializer\Expose
     * @Serializer\Groups({
     *      "carts_read",
     *      "carts_update",
     * })
     */
    private $quantity;


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
     * Set cart
     *
     * @param Cart $cart
     *
     * @return CartDetail
     */
    public function setCart(Cart $cart)
    {
        $this->cart = $cart;

        return $this;
    }

    /**
     * Get cart
     *
     * @return Cart
     */
    public function getCart()
    {
        return $this->cart;
    }

    /**
     * Set product
     *
     * @param Product $product
     *
     * @return CommandDetail
     */
    public function setProduct(Product $product)
    {
        $this->product = $product;

        return $this;
    }

    /**
     * Get product
     *
     * @return Product
     */
    public function getProduct()
    {
        return $this->product;
    }

    /**
     * Set quantity
     *
     * @param integer $quantity
     *
     * @return CartDetail
     */
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;

        return $this;
    }

    /**
     * Get quantity
     *
     * @return int
     */
    public function getQuantity()
    {
        return $this->quantity;
    }
}

