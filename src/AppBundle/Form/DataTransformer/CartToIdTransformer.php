<?php

namespace AppBundle\Form\DataTransformer;

use AppBundle\Entity\Cart;
use AppBundle\Manager\CartManager;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class CartToIdTransformer implements DataTransformerInterface
{
    /**
     * @var CartManager
     */
    private $cartManager;

    /**
     * @param CartManager $cartManager
     */
    public function __construct(CartManager $cartManager)
    {
        $this->cartManager = $cartManager;
    }

    /**
     * Transforms an object (cart) to an integer (id).
     *
     * @param Cart $cart
     *
     * @return int|null
     */
    public function transform($cart)
    {
        if (! ($cart instanceof Cart)) {
            return;
        }

        return $cart->getId();
    }

    /**
     * Transforms a integer (id) to an object (cart).
     *
     * @param int|string $id
     *
     * @return Cart|null
     *
     * @throws TransformationFailedException if object (cart) is not found
     */
    public function reverseTransform($id)
    {
        if (! is_numeric($id)) {
            return [];
        }

        $cart = $this->cartManager->find((int) $id);

        if (null === $cart) {
            throw new TransformationFailedException(
                sprintf(
                    'Cart with ID "%s" does not exist!',
                    $id
                )
            );
        }

        return $cart;
    }
}
