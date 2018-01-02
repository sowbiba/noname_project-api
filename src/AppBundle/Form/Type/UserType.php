<?php

namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
/**
 * FormType used to manage the creation and the update of users.
 */
class UserType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('active', 'AppBundle\Form\Type\BooleanType')
            ->add('firstname', 'Symfony\Component\Form\Extension\Core\Type\TextType')
            ->add('lastname', 'Symfony\Component\Form\Extension\Core\Type\TextType')
            ->add('phone', 'Symfony\Component\Form\Extension\Core\Type\TextType')
            ->add('address', 'Symfony\Component\Form\Extension\Core\Type\TextareaType')
            ->add(
                'birthdate',
                'Symfony\Component\Form\Extension\Core\Type\DateType',
                [
                    'format' => 'dd/MM/yyyy',
                    'widget' => 'single_text',
                    'input' => 'string',
                ]
            )
            ->add('email',      'Symfony\Component\Form\Extension\Core\Type\EmailType')
            ->add('username',      'Symfony\Component\Form\Extension\Core\Type\TextType')
            ->add('password',   'Symfony\Component\Form\Extension\Core\Type\PasswordType')
            ->add('role',       'AppBundle\Form\SelectorType\RoleSelectorType')
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'csrf_protection' => false,
            'data_class' => 'AppBundle\Entity\User',
        ]);
    }
}
