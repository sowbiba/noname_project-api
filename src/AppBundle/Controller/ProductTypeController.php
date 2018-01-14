<?php

namespace AppBundle\Controller;

use AppBundle\Entity\ProductType;
use AppBundle\Model\Collection;
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
 * ProductTypeController is a RESTful controller managing product types CRUD and listing.
 *
 * @Rest\NamePrefix("product_type_")
 */
class ProductTypeController extends ApiController
{
    /**
     * Gets the list of product types.
     *
     * <hr>
     *
     * After getting the initial list, use the <strong>first, last, next, prev</strong> link relations in the
     * <strong>_links</strong> property to get more product types in the list. Note that <strong>next</strong> will not be
     * available at the end of the list and <strong>prev</strong> will not be available at the start of the list. If
     * the results are exactly one page neither <strong>prev</strong> nor <strong>next</strong> will be available.
     *
     * The <strong>_embedded</strong> embedded product type resources key'ed by relation name.
     *
     * <hr>
     *
     * The filters allows you to use the percent sign and underscore wildcards (e.g. name, %name, name%, %name%,
     * na_e, n%e).
     *
     * @ApiDoc(
     *     section="Product Type",
     *     description="List product types",
     *     statusCodes={
     *         200="OK",
     *         400="Bad request",
     *         403="Forbidden",
     *     },
     *     filters={
     *         {
     *             "name"="id",
     *             "dataType"="integer",
     *             "description"="ID for the product type. Up to 11 characters.",
     *             "required"=false,
     *         },
     *         {
     *             "name"="name",
     *             "dataType"="string",
     *             "description"="Name for the product type. Up to 255 characters.",
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
    names are id and name. Valid orders are asc and desc. e.g. If you want the product types ordered by name in descending
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
     * @Rest\Get("/product-types")
     *
     * @ParamConverter("productTypes", class="AppBundle:ProductType", converter="collection_param_converter", options={"name"="productTypes"})
     *
     * @param Request    $request
     * @param Collection $productTypes
     *
     * @Security("is_granted('view')")
     *
     * @return View
     */
    public function listAction(Request $request, Collection $productTypes)
    {
        if ('' !== $fields = $request->query->get('fields', '')) {
            $fields = array_merge(explode(',', $fields), ['product_types']);
        }

        return $this
            ->view($productTypes, Response::HTTP_OK)
            ->setSerializationContext(
                SerializationContext::create()
                    ->setGroups(['Default', 'product_types_list'])
                    ->addExclusionStrategy(
                        new FieldsListExclusionStrategy('AppBundle\Entity\ProductType', $fields)
                    )
            );
    }

    /**
     * @ApiDoc(
     *     section="Product Type",
     *     description="Create new product type",
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
     *     },
     * )
     *
     * @Rest\Post("/product-types")
     *
     * @param Request $request
     *
     * @Security("is_granted('create')")
     *
     * @return View
     */
    public function createAction(Request $request)
    {
        $productType = $this->get('app.manager.product_type')->createAndSave($request->request->all());

        return $this
            ->view($productType, Response::HTTP_CREATED)
            ->setSerializationContext(
                SerializationContext::create()
                    ->setGroups(['Default', 'product_types_create'])
            );
    }


    /**
     * @ApiDoc(
     *     section="Product Type",
     *     description="Retrieve a product type",
     *     statusCodes={
     *         200="OK",
     *         403="Forbidden",
     *         404="Not found",
     *     },
     *     requirements={
     *         {
     *             "name"="product_type_id",
     *             "dataType"="integer",
     *             "description"="the product type id",
     *         }
     *     },
     * )
     *
     * @Rest\Get("/product-types/{product_type_id}")
     * @ParamConverter("productType", class="AppBundle:ProductType", options={"id"="product_type_id"})
     *
     * @param ProductType $productType
     *
     * @return View
     *
     * @Security("is_granted('view')")
     *
     * @throws EntityNotFoundException
     */
    public function readAction(ProductType $productType = null)
    {
        if (null === $productType) {
            throw new EntityNotFoundException();
        }

        return $this
            ->view($productType, Response::HTTP_OK)
            ->setSerializationContext(
                SerializationContext::create()
                    ->setGroups(['Default', 'product_types_read'])
            );
    }

    /**
     * @ApiDoc(
     *     section="Product Type",
     *     description="Update a product type",
     *     statusCodes={
     *         200="OK",
     *         403="Forbidden",
     *         404="Not found",
     *         422="Validation failed",
     *     },
     *     requirements={
     *         {
     *             "name"="product_type_id",
     *             "dataType"="integer",
     *             "description"="the product type id",
     *             "required"=true,
     *         }
     *     },
     *     parameters={
     *         {
     *             "name"="name",
     *             "dataType"="string",
     *             "description"="Name for the product type. Up to 255 characters.",
     *             "required"=false,
     *         },
     *     },
     * )
     *
     * @Rest\Put("/product-types/{product_type_id}")
     * @ParamConverter("productType", class="AppBundle:ProductType", options={"id"="product_type_id"})
     *
     * @param Request $request
     * @param ProductType   $productType
     *
     * @return View
     *
     * @Security("is_granted('edit')")
     *
     * @throws EntityNotFoundException
     */
    public function updateAction(Request $request, ProductType $productType = null)
    {
        if (null === $productType) {
            throw new EntityNotFoundException();
        }

        $productType = $this->get('app.manager.product_type')->updateAndSave($productType, $request->request->all());

        return $this
            ->view($productType, Response::HTTP_OK)
            ->setSerializationContext(
                SerializationContext::create()
                    ->setGroups(['Default', 'product_types_update'])
            );
    }
    /**
     * @ApiDoc(
     *     section="Product Type",
     *     description="Delete a product type",
     *     statusCodes={
     *         204="OK",
     *         403="Forbidden",
     *         404="Not found",
     *     },
     *     requirements={
     *         {
     *             "name"="product_type_id",
     *             "dataType"="integer",
     *             "description"="the product type id",
     *         }
     *     },
     * )
     *
     * @Rest\Delete("/product-types/{product_type_id}")
     * @ParamConverter("productType", class="AppBundle:ProductType", options={"id"="product_type_id"})
     *
     * @param ProductType $productType
     *
     * @Security("is_granted('delete')")
     *
     * @return View
     *
     * @throws EntityNotFoundException
     */
    public function deleteAction(ProductType $productType = null)
    {
        if (null === $productType) {
            throw new EntityNotFoundException();
        }

        $this->get('app.manager.product_type')->delete($productType);

        return $this->view(null, Response::HTTP_NO_CONTENT);
    }
}
