<?php

namespace AppBundle\Form\SelectorType;

use AppBundle\Manager\ProductTypeManager;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use AppBundle\Form\DataTransformer\ProductTypeToIdTransformer;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductTypeSelectorType extends AbstractType
{
    /**
     * @var ProductTypeManager
     */
    public $productTypeManager;

    /**
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->productTypeManager = $container->get('app.manager.product_type');
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addModelTransformer(new ProductTypeToIdTransformer($this->productTypeManager));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'invalid_message' => 'validation.field.invalid.product_types',
            ]
        );
    }

    public function getParent()
    {
        return 'Symfony\Component\Form\Extension\Core\Type\IntegerType';
    }
}
