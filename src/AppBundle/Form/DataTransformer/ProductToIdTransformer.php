<?php

namespace AppBundle\Form\DataTransformer;

use AppBundle\Entity\Product;
use AppBundle\Manager\ProductManager;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class ProductToIdTransformer implements DataTransformerInterface
{
    /**
     * @var ProductManager
     */
    private $productManager;

    /**
     * @param ProductManager $productManager
     */
    public function __construct(ProductManager $productManager)
    {
        $this->productManager = $productManager;
    }

    /**
     * Transforms an object (product) to an integer (id).
     *
     * @param Product $product
     *
     * @return int|null
     */
    public function transform($product)
    {
        if (! ($product instanceof Product)) {
            return;
        }

        return $product->getId();
    }

    /**
     * Transforms a integer (id) to an object (product).
     *
     * @param int|string $id
     *
     * @return Product|null
     *
     * @throws TransformationFailedException if object (product) is not found
     */
    public function reverseTransform($id)
    {
        if (! is_numeric($id)) {
            return [];
        }

        $product = $this->productManager->find((int) $id);

        if (null === $product) {
            throw new TransformationFailedException(
                sprintf(
                    'Product with ID "%s" does not exist!',
                    $id
                )
            );
        }

        return $product;
    }
}
