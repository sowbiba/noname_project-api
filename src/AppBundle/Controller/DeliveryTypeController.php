<?php

namespace AppBundle\Controller;

use AppBundle\Entity\DeliveryType;
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
 * DeliveryTypeController is a RESTful controller managing delivery types CRUD and listing.
 *
 * @Rest\NamePrefix("delivery_type_")
 */
class DeliveryTypeController extends ApiController
{
    /**
     * Gets the list of delivery types.
     *
     * <hr>
     *
     * After getting the initial list, use the <strong>first, last, next, prev</strong> link relations in the
     * <strong>_links</strong> property to get more delivery types in the list. Note that <strong>next</strong> will not be
     * available at the end of the list and <strong>prev</strong> will not be available at the start of the list. If
     * the results are exactly one page neither <strong>prev</strong> nor <strong>next</strong> will be available.
     *
     * The <strong>_embedded</strong> embedded delivery type resources key'ed by relation name.
     *
     * <hr>
     *
     * The filters allows you to use the percent sign and underscore wildcards (e.g. name, %name, name%, %name%,
     * na_e, n%e).
     *
     * @ApiDoc(
     *     section="Delivery Type",
     *     description="List delivery types",
     *     statusCodes={
     *         200="OK",
     *         400="Bad request",
     *         403="Forbidden",
     *     },
     *     filters={
     *         {
     *             "name"="id",
     *             "dataType"="integer",
     *             "description"="ID for the delivery type. Up to 11 characters.",
     *             "required"=false,
     *         },
     *         {
     *             "name"="name",
     *             "dataType"="string",
     *             "description"="Name for the delivery type. Up to 255 characters.",
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
    names are id and name. Valid orders are asc and desc. e.g. If you want the delivery types ordered by name in descending
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
     * @Rest\Get("/delivery-types")
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
            $fields = array_merge(explode(',', $fields), ['delivery_types']);
        }

        $deliveryTypes = $this->get('app.manager.delivery_type')->findAll();

        return $this
            ->view($deliveryTypes, Response::HTTP_OK)
            ->setSerializationContext(
                SerializationContext::create()
                    ->setGroups(['Default', 'delivery_types_list'])
                    ->addExclusionStrategy(
                        new FieldsListExclusionStrategy('AppBundle\Entity\DeliveryType', $fields)
                    )
            );
    }

    /**
     * @ApiDoc(
     *     section="Delivery Type",
     *     description="Create new delivery type",
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
     *             "name"="delay",
     *             "dataType"="integer",
     *             "description"="Delay for the delivery. Up to 11 digits.",
     *             "required"=false,
     *         },
     *         {
     *             "name"="price",
     *             "dataType"="integer",
     *             "description"="The delivery cost. Up to 11 digits.",
     *             "required"=false,
     *         },
     *     },
     * )
     *
     * @Rest\Post("/delivery-types")
     *
     * @param Request $request
     *
     * @Security("is_granted('create')")
     *
     * @return View
     */
    public function createAction(Request $request)
    {
        $deliveryType = $this->get('app.manager.delivery_type')->createAndSave($request->request->all());

        return $this
            ->view($deliveryType, Response::HTTP_CREATED)
            ->setSerializationContext(
                SerializationContext::create()
                    ->setGroups(['Default', 'delivery_types_create'])
            );
    }


    /**
     * @ApiDoc(
     *     section="Delivery Type",
     *     description="Retrieve a delivery type",
     *     statusCodes={
     *         200="OK",
     *         403="Forbidden",
     *         404="Not found",
     *     },
     *     requirements={
     *         {
     *             "name"="delivery_type_id",
     *             "dataType"="integer",
     *             "description"="the delivery type id",
     *         }
     *     },
     * )
     *
     * @Rest\Get("/delivery-types/{delivery_type_id}")
     * @ParamConverter("deliveryType", class="AppBundle:DeliveryType", options={"id"="delivery_type_id"})
     *
     * @param DeliveryType $deliveryType
     *
     * @return View
     *
     * @Security("is_granted('view')")
     *
     * @throws EntityNotFoundException
     */
    public function readAction(DeliveryType $deliveryType = null)
    {
        if (null === $deliveryType) {
            throw new EntityNotFoundException();
        }

        return $this
            ->view($deliveryType, Response::HTTP_OK)
            ->setSerializationContext(
                SerializationContext::create()
                    ->setGroups(['Default', 'delivery_types_read'])
            );
    }

    /**
     * @ApiDoc(
     *     section="Delivery Type",
     *     description="Update a delivery type",
     *     statusCodes={
     *         200="OK",
     *         403="Forbidden",
     *         404="Not found",
     *         422="Validation failed",
     *     },
     *     requirements={
     *         {
     *             "name"="delivery_type_id",
     *             "dataType"="integer",
     *             "description"="the delivery type id",
     *             "required"=true,
     *         }
     *     },
     *     parameters={
     *         {
     *             "name"="name",
     *             "dataType"="string",
     *             "description"="Name for the delivery type. Up to 255 characters.",
     *             "required"=false,
     *         },
     *         {
     *             "name"="delay",
     *             "dataType"="string",
     *             "description"="Delay for the delivery type. Up to 11 digits.",
     *             "required"=true,
     *         },
     *         {
     *             "name"="price",
     *             "dataType"="string",
     *             "description"="Price of the delivery type. Up to 11 digits.",
     *             "required"=true,
     *         },
     *     },
     * )
     *
     * @Rest\Put("/delivery-types/{delivery_type_id}")
     * @ParamConverter("deliveryType", class="AppBundle:DeliveryType", options={"id"="delivery_type_id"})
     *
     * @param Request $request
     * @param DeliveryType   $deliveryType
     *
     * @return View
     *
     * @Security("is_granted('edit')")
     *
     * @throws EntityNotFoundException
     */
    public function updateAction(Request $request, DeliveryType $deliveryType = null)
    {
        if (null === $deliveryType) {
            throw new EntityNotFoundException();
        }

        $deliveryType = $this->get('app.manager.delivery_type')->updateAndSave($deliveryType, $request->request->all());

        return $this
            ->view($deliveryType, Response::HTTP_OK)
            ->setSerializationContext(
                SerializationContext::create()
                    ->setGroups(['Default', 'delivery_types_update'])
            );
    }
    /**
     * @ApiDoc(
     *     section="Delivery Type",
     *     description="Delete a delivery type",
     *     statusCodes={
     *         204="OK",
     *         403="Forbidden",
     *         404="Not found",
     *     },
     *     requirements={
     *         {
     *             "name"="delivery_type_id",
     *             "dataType"="integer",
     *             "description"="the delivery type id",
     *         }
     *     },
     * )
     *
     * @Rest\Delete("/delivery-types/{delivery_type_id}")
     * @ParamConverter("deliveryType", class="AppBundle:DeliveryType", options={"id"="delivery_type_id"})
     *
     * @param DeliveryType $deliveryType
     *
     * @Security("is_granted('delete')")
     *
     * @return View
     *
     * @throws EntityNotFoundException
     */
    public function deleteAction(DeliveryType $deliveryType = null)
    {
        if (null === $deliveryType) {
            throw new EntityNotFoundException();
        }

        $this->get('app.manager.delivery_type')->delete($deliveryType);

        return $this->view(null, Response::HTTP_NO_CONTENT);
    }
}
