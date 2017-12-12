<?php

namespace AppBundle\Factory;

use AppBundle\Exception\ManagerNotFoundException;
use AppBundle\Manager as Manager;
use Symfony\Component\DependencyInjection\ContainerInterface;

class ManagerFactory
{
    const MANAGER_DELIVERY_TYPE = 'delivery_type';
    const MANAGER_PRODUCT_TYPE = 'product_type';
    const MANAGER_USER = 'user';
    const MANAGER_ROLE = 'role';
    const MANAGER_PRODUCT = 'product';
    const MANAGER_STOCK = 'stock';
    const MANAGER_COMMAND = 'command';
    const MANAGER_CART = 'cart';
    const MANAGER_CART_DETAIL = 'cart_detail';

    /**
     * @param ContainerInterface $container
     * @param string             $type
     *
     * @return Manager\EntityManager
     *
     * @throws ManagerNotFoundException
     */
    public static function get(ContainerInterface $container, $type)
    {
        $manager = null;

        switch ($type) {
            case static::MANAGER_DELIVERY_TYPE:
                $form = $container->get('form.factory')->create('AppBundle\Form\Type\DeliveryTypeType');

                // todo: Use DeliveryType::class when the PHP version is >= 5.5
                $manager = new Manager\DeliveryTypeManager($container, 'AppBundle\Entity\DeliveryType', $form);
                break;
            case static::MANAGER_PRODUCT_TYPE:
                $form = $container->get('form.factory')->create('AppBundle\Form\Type\ProductTypeType');

                // todo: Use ProductType::class when the PHP version is >= 5.5
                $manager = new Manager\ProductTypeManager($container, 'AppBundle\Entity\ProductType', $form);
                break;
            case static::MANAGER_USER:
                $form = $container->get('form.factory')->create('AppBundle\Form\Type\UserType');

                // todo: Use User::class when the PHP version is >= 5.5
                $manager = new Manager\UserManager($container, 'AppBundle\Entity\User', $form);
                break;
            case static::MANAGER_ROLE:
                $form = $container->get('form.factory')->create('AppBundle\Form\Type\RoleType');

                // todo: Use Role::class when the PHP version is >= 5.5
                $manager = new Manager\RoleManager($container, 'AppBundle\Entity\Role', $form);
                break;
            case static::MANAGER_PRODUCT:
                $form = $container->get('form.factory')->create('AppBundle\Form\Type\ProductType');

                // todo: Use Product::class when the PHP version is >= 5.5
                $manager = new Manager\ProductManager($container, 'AppBundle\Entity\Product', $form);
                break;
            case static::MANAGER_STOCK:
                $form = $container->get('form.factory')->create('AppBundle\Form\Type\StockType');

                // todo: Use Stock::class when the PHP version is >= 5.5
                $manager = new Manager\StockManager($container, 'AppBundle\Entity\Stock', $form);
                break;
            case static::MANAGER_COMMAND:
                $form = $container->get('form.factory')->create('AppBundle\Form\Type\CommandType');

                // todo: Use Command::class when the PHP version is >= 5.5
                $manager = new Manager\CommandManager($container, 'AppBundle\Entity\Command', $form);
                break;
            case static::MANAGER_CART:
                $form = $container->get('form.factory')->create('AppBundle\Form\Type\CartType');

                // todo: Use Cart::class when the PHP version is >= 5.5
                $manager = new Manager\CartManager($container, 'AppBundle\Entity\Cart', $form);
                break;
            case static::MANAGER_CART_DETAIL:
                $form = $container->get('form.factory')->create('AppBundle\Form\Type\CartDetailType');

                // todo: Use CartDetail::class when the PHP version is >= 5.5
                $manager = new Manager\CartDetailManager($container, 'AppBundle\Entity\CartDetail', $form);
                break;
            default:
                throw new ManagerNotFoundException(sprintf("The '%s' manager does not exists", $type));
        }

        return $manager;
    }
}
