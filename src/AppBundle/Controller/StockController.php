<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Product;
use AppBundle\Entity\Stock;
use AppBundle\Entity\User;
use AppBundle\Serializer\Exclusion\FieldsListExclusionStrategy;
use Doctrine\ORM\EntityNotFoundException;
use FOS\RestBundle\Context\Context;
use JMS\Serializer\SerializationContext;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\View\View;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use FOS\RestBundle\Controller\Annotations as Rest;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

/**
 * StockController is a RESTful controller managing stocks CRUD and listing.
 *
 * @Rest\NamePrefix("stock_")
 */
class StockController extends ApiController
{
    /**
     * @ApiDoc(
     *     section="Stock",
     *     description="Retrieve a stock",
     *     statusCodes={
     *         200="OK",
     *         403="Forbidden",
     *         404="Not found",
     *     },
     *     requirements={
     *         {
     *             "name"="product_id",
     *             "dataType"="integer",
     *             "description"="the product id",
     *         }
     *     },
     * )
     *
     * @Rest\Get("/product/{product_id}/stock")
     * @ParamConverter("product", class="AppBundle:Product", options={"id"="product_id"})
     *
     * @param Product $product
     *
     * @return View
     *
     * @Security("is_granted('view')")
     *
     * @throws EntityNotFoundException
     */
    public function readAction(Product $product = null)
    {
        if (null === $product) {
            throw new EntityNotFoundException();
        }

        if (null === $stock = $product->getStock()) {
            throw new EntityNotFoundException();
        }

        return $this
            ->view($stock, Response::HTTP_OK)
            ->setSerializationContext(
                SerializationContext::create()
                    ->setGroups(['Default', 'stocks_read'])
            );
    }



    /**
     * @ApiDoc(
     *     section="Stock",
     *     description="Update a stock",
     *     statusCodes={
     *         200="OK",
     *         403="Forbidden",
     *         404="Not found",
     *         422="Validation failed",
     *     },
     *     requirements={
     *         {
     *             "name"="product_id",
     *             "dataType"="integer",
     *             "description"="the product id",
     *             "required"=true,
     *         }
     *     },
     *     parameters={
     *         {
     *             "name"="quantity",
     *             "dataType"="integer",
     *             "description"="Quantity of products available on stock. Up to 11 digits.",
     *             "required"=false,
     *         },
     *     },
     * )
     *
     * @Rest\Put("/product/{product_id}/stock")
     * @ParamConverter("product", class="AppBundle:Product", options={"id"="product_id"})
     *
     * @param Request $request
     * @param Product $product
     *
     * @return View
     *
     * @Security("is_granted('edit')")
     *
     * @throws EntityNotFoundException
     */
    public function updateAction(Request $request, Product $product = null)
    {
        if (null === $product) {
            throw new EntityNotFoundException();
        }

        if (null === $stock = $product->getStock()) {
            throw new EntityNotFoundException();
        }

        $stock = $this->get('app.manager.stock')->updateAndSave($stock, $request->request->all());

        return $this
            ->view($stock, Response::HTTP_OK)
            ->setSerializationContext(
                SerializationContext::create()
                    ->setGroups(['Default', 'stocks_update'])
            );
    }
}
