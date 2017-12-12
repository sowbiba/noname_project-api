<?php

namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
/**
 * FormType used to manage the creation and the update of products.
 */
class ProductType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name',       'Symfony\Component\Form\Extension\Core\Type\TextType')
            ->add('description','Symfony\Component\Form\Extension\Core\Type\TextareaType')
            ->add('price',      'Symfony\Component\Form\Extension\Core\Type\IntegerType')
            ->add('photoFile',  'Symfony\Component\Form\Extension\Core\Type\TextType')
            ->add('productType','AppBundle\Form\SelectorType\ProductTypeSelectorType')
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'csrf_protection' => false,
            'data_class' => 'AppBundle\Entity\Product',
        ]);
    }
}
