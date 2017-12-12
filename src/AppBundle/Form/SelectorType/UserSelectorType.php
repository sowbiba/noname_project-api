<?php

namespace AppBundle\Form\SelectorType;

use AppBundle\Manager\UserManager;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use AppBundle\Form\DataTransformer\UserToIdTransformer;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserSelectorType extends AbstractType
{
    /**
     * @var UserManager
     */
    public $userManager;

    /**
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->userManager = $container->get('app.manager.user');
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addModelTransformer(new UserToIdTransformer($this->userManager));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'invalid_message' => 'validation.field.invalid.user',
            ]
        );
    }

    public function getParent()
    {
        return 'Symfony\Component\Form\Extension\Core\Type\IntegerType';
    }
}
