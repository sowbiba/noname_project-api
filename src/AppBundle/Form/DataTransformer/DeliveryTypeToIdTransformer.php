<?php

namespace AppBundle\Form\DataTransformer;

use AppBundle\Entity\DeliveryType;
use AppBundle\Manager\DeliveryTypeManager;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class DeliveryTypeToIdTransformer implements DataTransformerInterface
{
    /**
     * @var DeliveryTypeManager
     */
    private $deliveryTypeManager;

    /**
     * @param DeliveryTypeManager $deliveryTypeManager
     */
    public function __construct(DeliveryTypeManager $deliveryTypeManager)
    {
        $this->deliveryTypeManager = $deliveryTypeManager;
    }

    /**
     * Transforms an object (deliveryType) to an integer (id).
     *
     * @param DeliveryType $deliveryType
     *
     * @return int|null
     */
    public function transform($deliveryType)
    {
        if (! ($deliveryType instanceof DeliveryType)) {
            return;
        }

        return $deliveryType->getId();
    }

    /**
     * Transforms a integer (id) to an object (deliveryType).
     *
     * @param int|string $id
     *
     * @return DeliveryType|null
     *
     * @throws TransformationFailedException if object (deliveryType) is not found
     */
    public function reverseTransform($id)
    {
        if (! is_numeric($id)) {
            return [];
        }

        $deliveryType = $this->deliveryTypeManager->find((int) $id);

        if (null === $deliveryType) {
            throw new TransformationFailedException(
                sprintf(
                    'DeliveryType with ID "%s" does not exist!',
                    $id
                )
            );
        }

        return $deliveryType;
    }
}
