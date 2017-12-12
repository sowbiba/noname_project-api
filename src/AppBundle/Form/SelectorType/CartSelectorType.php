<?php

namespace AppBundle\Form\SelectorType;

use AppBundle\Manager\CartManager;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use AppBundle\Form\DataTransformer\CartToIdTransformer;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CartSelectorType extends AbstractType
{
    /**
     * @var CartManager
     */
    public $cartManager;

    /**
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->cartManager = $container->get('app.manager.cart');
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addModelTransformer(new CartToIdTransformer($this->cartManager));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'invalid_message' => 'validation.field.invalid.cart',
            ]
        );
    }

    public function getParent()
    {
        return 'Symfony\Component\Form\Extension\Core\Type\IntegerType';
    }
}
