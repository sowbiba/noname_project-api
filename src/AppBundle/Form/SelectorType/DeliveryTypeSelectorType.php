<?php

namespace AppBundle\Form\SelectorType;

use AppBundle\Manager\DeliveryTypeManager;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use AppBundle\Form\DataTransformer\DeliveryTypeToIdTransformer;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DeliveryTypeSelectorType extends AbstractType
{
    /**
     * @var DeliveryTypeManager
     */
    public $deliveryTypeManager;

    /**
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->deliveryTypeManager = $container->get('app.manager.delivery_type');
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addModelTransformer(new DeliveryTypeToIdTransformer($this->deliveryTypeManager));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'invalid_message' => 'validation.field.invalid.delivery_types',
            ]
        );
    }

    public function getParent()
    {
        return 'Symfony\Component\Form\Extension\Core\Type\IntegerType';
    }
}
