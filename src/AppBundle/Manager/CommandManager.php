<?php
namespace AppBundle\Manager;


use AppBundle\Entity\Cart;
use AppBundle\Entity\CartDetail;
use AppBundle\Entity\Command;
use AppBundle\Entity\CommandDetail;
use AppBundle\Entity\DeliveryType;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;

class CommandManager extends CRUDManager
{

    /**
     * @param Cart $cart
     * @param array $parameters
     *
     * @return Command
     */
    public function createFromCart(Cart $cart, $parameters = array())
    {
        if ($cart->getCartDetails()->isEmpty()) {
            throw new UnprocessableEntityHttpException("Cart is empty");
        }

        /**
         * @var Command $command
         */
        $command = $this->createEmpty();

        /**
         * @var CartDetail $cartDetail
         */
        foreach ($cart->getCartDetails()->toArray() as $cartDetail) {
            $commandDetail = new CommandDetail();
            $commandDetail->setCommand($command);
            $commandDetail->setProduct($cartDetail->getProduct());
            $commandDetail->setQuantity($cartDetail->getQuantity());

            $command->addCommandDetail($commandDetail);

            $command->setTotal($command->getTotal() + ($cartDetail->getProduct()->getPrice() * $cartDetail->getQuantity()));
        }

        foreach ($parameters as $parameterName => $parameterValue) {
            switch ($parameterName) {
                case 'deliveryType':
                    if (is_numeric($parameterValue)) {
                        $deliveryType = $this->container->get('app.manager.delivery_type')->find(intval($parameterValue));

                        /**
                         * @var DeliveryType $deliveryType
                         */
                        if (null !== $deliveryType) {
                            $command->setDeliveryType($deliveryType);
                            $command->setTotal($command->getTotal() + $deliveryType->getPrice());
                        }
                    }

                    break;
            }
        }

        $command->setFactureFile($this->generateFacture($command));

        $this->save($command);

        return $command;
    }

    private function generateFacture(Command $command)
    {
        /**
         * @TODO generate the pdf
         */
        return "toto.pdf";
    }
} 