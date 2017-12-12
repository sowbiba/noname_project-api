<?php

namespace AppBundle\Form\SelectorType;

use AppBundle\Manager\RoleManager;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use AppBundle\Form\DataTransformer\RoleToIdTransformer;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RoleSelectorType extends AbstractType
{
    /**
     * @var RoleManager
     */
    public $roleManager;

    /**
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->roleManager = $container->get('app.manager.role');
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addModelTransformer(new RoleToIdTransformer($this->roleManager));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'invalid_message' => 'validation.field.invalid.role',
            ]
        );
    }

    public function getParent()
    {
        return 'Symfony\Component\Form\Extension\Core\Type\IntegerType';
    }
}
