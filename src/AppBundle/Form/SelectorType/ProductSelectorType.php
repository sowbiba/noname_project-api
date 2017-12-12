<?php

namespace AppBundle\Form\SelectorType;

use AppBundle\Manager\ProductManager;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use AppBundle\Form\DataTransformer\ProductToIdTransformer;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductSelectorType extends AbstractType
{
    /**
     * @var ProductManager
     */
    public $productManager;

    /**
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->productManager = $container->get('app.manager.product');
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addModelTransformer(new ProductToIdTransformer($this->productManager));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'invalid_message' => 'validation.field.invalid.product',
            ]
        );
    }

    public function getParent()
    {
        return 'Symfony\Component\Form\Extension\Core\Type\IntegerType';
    }
}
