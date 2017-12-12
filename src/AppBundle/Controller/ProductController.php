<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Product;
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
 * ProductController is a RESTful controller managing products CRUD and listing.
 *
 * @Rest\NamePrefix("product_")
 */
class ProductController extends ApiController
{
    /**
     * Gets the list of products.
     *
     * <hr>
     *
     * After getting the initial list, use the <strong>first, last, next, prev</strong> link relations in the
     * <strong>_links</strong> property to get more products in the list. Note that <strong>next</strong> will not be
     * available at the end of the list and <strong>prev</strong> will not be available at the start of the list. If
     * the results are exactly one page neither <strong>prev</strong> nor <strong>next</strong> will be available.
     *
     * The <strong>_embedded</strong> embedded product resources key'ed by relation name.
     *
     * <hr>
     *
     * The filters allows you to use the percent sign and underscore wildcards (e.g. name, %name, name%, %name%,
     * na_e, n%e).
     *
     * @ApiDoc(
     *     section="Product",
     *     description="List products",
     *     statusCodes={
     *         200="OK",
     *         400="Bad request",
     *         403="Forbidden",
     *     },
     *     filters={
     *         {
     *             "name"="id",
     *             "dataType"="integer",
     *             "description"="ID for the product. Up to 11 characters.",
     *             "required"=false,
     *         },
     *         {
     *             "name"="name",
     *             "dataType"="string",
     *             "description"="Name for the product. Up to 255 characters.",
     *             "required"=false,
     *         },
     *         {
     *             "name"="productType",
     *             "dataType"="integer",
     *             "description"="Product's type id. Up to 11 digits.",
     *             "required"=false,
     *         },
     *     },
     *     parameters={
     *         {
     *             "name"="fields",
     *             "dataType"="string",
     *             "description"="
    Specify the fields that will be returned using the format FIELD_NAME[, FIELD_NAME ...]. Valid fields are id and
    name. e.g. If you want the result with the name field only, the fields string would be name.
    Default is: all the fields.",
     *             "required"=false,
     *         },
     *         {
     *             "name"="orderBy",
     *             "dataType"="string",
     *             "description"="
    Specify the order criteria of the result using the format COLUMN_NAME ORDER[, COLUMN_NAME ORDER ...]. Valid column
    names are id and name. Valid orders are asc and desc. e.g. If you want the products ordered by name in descending
    order and then order by id in ascending order, the order string would be name=desc, id=asc.
    Default is: id asc.",
     *             "required"=false,
     *         },
     *         {
     *             "name"="page",
     *             "dataType"="integer",
     *             "description"="
    Current page to returned.
    Default is: 1.",
     *             "required"=false,
     *         },
     *         {
     *             "name"="limit",
     *             "dataType"="integer",
     *             "description"="
    Maximum number of items requested (-1 for no limit).
    Default is: 20.",
     *             "required"=false,
     *         },
     *     },
     * )
     *
     * @Rest\Get("/products")
     *
     * @param Request    $request
     *
     * @Security("is_granted('view')")
     *
     * @return View
     */
    public function listAction(Request $request)
    {
        if ('' !== $fields = $request->query->get('fields', '')) {
            $fields = array_merge(explode(',', $fields), ['products']);
        }

        $products = $this->get('app.manager.product')->findAll();

        return $this
            ->view($products, Response::HTTP_OK)
            ->setSerializationContext(
                SerializationContext::create()
                    ->setGroups(['Default', 'products_list'])
                    ->addExclusionStrategy(
                        new FieldsListExclusionStrategy('AppBundle\Entity\Product', $fields)
                    )
            );
    }

    /**
     * @ApiDoc(
     *     section="Product",
     *     description="Create new product",
     *     statusCodes={
     *         201="Created",
     *         400="Bad request",
     *         403="Forbidden",
     *         422="Validation failed",
     *     },
     *     parameters={
     *         {
     *             "name"="name",
     *             "dataType"="string",
     *             "description"="Name for the group. Up to 255 characters.",
     *             "required"=false,
     *         },
     *         {
     *             "name"="description",
     *             "dataType"="text",
     *             "description"="The product description.",
     *             "required"=false,
     *         },
     *         {
     *             "name"="price",
     *             "dataType"="integer",
     *             "description"="The product cost. Up to 11 digits.",
     *             "required"=false,
     *         },
     *         {
     *             "name"="photoFile",
     *             "dataType"="string",
     *             "description"="Photo file path of the product. Up to 255 characters.",
     *             "required"=false,
     *         },
     *         {
     *             "name"="productType",
     *             "dataType"="integer",
     *             "description"="Product's type id. Up to 11 digits.",
     *             "required"=false,
     *         },
     *     },
     * )
     *
     * @Rest\Post("/products")
     *
     * @param Request $request
     *
     * @Security("is_granted('create')")
     *
     * @return View
     */
    public function createAction(Request $request)
    {
        $product = $this->get('app.manager.product')->createAndSave($request->request->all());

        return $this
            ->view($product, Response::HTTP_CREATED)
            ->setSerializationContext(
                SerializationContext::create()
                    ->setGroups(['Default', 'products_create'])
            );
    }


    /**
     * @ApiDoc(
     *     section="Product",
     *     description="Retrieve a product",
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
     * @Rest\Get("/products/{product_id}")
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

        return $this
            ->view($product, Response::HTTP_OK)
            ->setSerializationContext(
                SerializationContext::create()
                    ->setGroups(['Default', 'products_read'])
            );
    }

    /**
     * @ApiDoc(
     *     section="Product",
     *     description="Update a product",
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
     *             "name"="name",
     *             "dataType"="string",
     *             "description"="Name for the group. Up to 255 characters.",
     *             "required"=false,
     *         },
     *         {
     *             "name"="description",
     *             "dataType"="text",
     *             "description"="The product description.",
     *             "required"=false,
     *         },
     *         {
     *             "name"="price",
     *             "dataType"="integer",
     *             "description"="The product cost. Up to 11 digits.",
     *             "required"=false,
     *         },
     *         {
     *             "name"="photoFile",
     *             "dataType"="string",
     *             "description"="Photo file path of the product. Up to 255 characters.",
     *             "required"=false,
     *         },
     *         {
     *             "name"="productType",
     *             "dataType"="integer",
     *             "description"="Product's type id. Up to 11 digits.",
     *             "required"=false,
     *         },
     *     },
     * )
     *
     * @Rest\Put("/products/{product_id}")
     * @ParamConverter("product", class="AppBundle:Product", options={"id"="product_id"})
     *
     * @param Request $request
     * @param Product   $product
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

        $product = $this->get('app.manager.product')->updateAndSave($product, $request->request->all());

        return $this
            ->view($product, Response::HTTP_OK)
            ->setSerializationContext(
                SerializationContext::create()
                    ->setGroups(['Default', 'products_update'])
            );
    }

    /**
     * @ApiDoc(
     *     section="Product",
     *     description="Delete a product",
     *     statusCodes={
     *         204="OK",
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
     * @Rest\Delete("/products/{product_id}")
     * @ParamConverter("product", class="AppBundle:Product", options={"id"="product_id"})
     *
     * @param Product $product
     *
     * @return View
     *
     * @Security("is_granted('delete')")
     *
     * @throws EntityNotFoundException
     */
    public function deleteAction(Product $product = null)
    {
        if (null === $product) {
            throw new EntityNotFoundException();
        }

        $this->get('app.manager.product')->delete($product);

        return $this->view(null, Response::HTTP_NO_CONTENT);
    }
}
