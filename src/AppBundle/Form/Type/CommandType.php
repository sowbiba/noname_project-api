<?php

namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
/**
 * FormType used to manage the creation and the update of commands.
 */
class CommandType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('user',           'AppBundle\Form\SelectorType\UserSelectorType')
            ->add('total',          'Symfony\Component\Form\Extension\Core\Type\MoneyType')
            ->add('deliveryType',   'AppBundle\Form\SelectorType\DeliveryTypeSelectorType')
            ->add('deliveryStatus', 'Symfony\Component\Form\Extension\Core\Type\TextType')
            ->add('factureFile',    'Symfony\Component\Form\Extension\Core\Type\TextType')
            ->add('deliveredAt',    'Symfony\Component\Form\Extension\Core\Type\DateType',
                [
                    'format' => 'dd/MM/yyyy',
                    'widget' => 'single_text',
                ]
            )
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'csrf_protection' => false,
            'data_class' => 'AppBundle\Entity\Command',
        ]);
    }
}
