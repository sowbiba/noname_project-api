<?php

namespace AppBundle\Form\DataTransformer;

use AppBundle\Entity\ProductType;
use AppBundle\Manager\ProductTypeManager;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class ProductTypeToIdTransformer implements DataTransformerInterface
{
    /**
     * @var ProductTypeManager
     */
    private $productTypeManager;

    /**
     * @param ProductTypeManager $productTypeManager
     */
    public function __construct(ProductTypeManager $productTypeManager)
    {
        $this->productTypeManager = $productTypeManager;
    }

    /**
     * Transforms an object (productType) to an integer (id).
     *
     * @param ProductType $productType
     *
     * @return int|null
     */
    public function transform($productType)
    {
        if (! ($productType instanceof ProductType)) {
            return;
        }

        return $productType->getId();
    }

    /**
     * Transforms a integer (id) to an object (productType).
     *
     * @param int|string $id
     *
     * @return ProductType|null
     *
     * @throws TransformationFailedException if object (productType) is not found
     */
    public function reverseTransform($id)
    {
        if (! is_numeric($id)) {
            return [];
        }

        $productType = $this->productTypeManager->find((int) $id);

        if (null === $productType) {
            throw new TransformationFailedException(
                sprintf(
                    'ProductType with ID "%s" does not exist!',
                    $id
                )
            );
        }

        return $productType;
    }
}
