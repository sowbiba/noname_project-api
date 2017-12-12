<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Role;
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
 * RoleController is a RESTful controller managing users CRUD and listing.
 *
 * @Rest\NamePrefix("role_")
 */
class RoleController extends ApiController
{
    /**
     * Gets the list of roles.
     *
     * <hr>
     *
     * After getting the initial list, use the <strong>first, last, next, prev</strong> link relations in the
     * <strong>_links</strong> property to get more roles in the list. Note that <strong>next</strong> will not be
     * available at the end of the list and <strong>prev</strong> will not be available at the start of the list. If
     * the results are exactly one page neither <strong>prev</strong> nor <strong>next</strong> will be available.
     *
     * The <strong>_embedded</strong> embedded role resources key'ed by relation name.
     *
     * <hr>
     *
     * The filters allows you to use the percent sign and underscore wildcards (e.g. name, %name, name%, %name%,
     * na_e, n%e).
     *
     * @ApiDoc(
     *     section="Role",
     *     description="List roles",
     *     statusCodes={
     *         200="OK",
     *         400="Bad request",
     *         403="Forbidden",
     *     },
     *     filters={
     *         {
     *             "name"="id",
     *             "dataType"="integer",
     *             "description"="ID for the role. Up to 11 characters.",
     *             "required"=false,
     *         },
     *         {
     *             "name"="name",
     *             "dataType"="string",
     *             "description"="Name of the role. Up to 255 characters.",
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
    names are id and name. Valid orders are asc and desc. e.g. If you want the roles ordered by name in descending
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
     * @Rest\Get("/roles")
     *
     * @ParamConverter("roles", class="AppBundle:Role", converter="collection_param_converter", options={"name"="roles"})
     *
     * @param Request    $request
     *
     * @Security("is_granted('edit')")
     *
     * @return View
     */
    public function listAction(Request $request, Collection $roles)
    {
        if ('' !== $fields = $request->query->get('fields', '')) {
            $fields = array_merge(explode(',', $fields), ['roles']);
        }

        return $this
            ->view($roles, Response::HTTP_OK)
            ->setSerializationContext(
                SerializationContext::create()
                    ->setGroups(['Default', 'roles_list'])
                    ->addExclusionStrategy(
                        new FieldsListExclusionStrategy('AppBundle\Entity\Role', $fields)
                    )
            );
    }


    /**
     * @ApiDoc(
     *     section="Role",
     *     description="Retrieve a role",
     *     statusCodes={
     *         200="OK",
     *         403="Forbidden",
     *         404="Not found",
     *     },
     *     requirements={
     *         {
     *             "name"="role_id",
     *             "dataType"="integer",
     *             "description"="the role id",
     *         }
     *     },
     * )
     *
     * @Rest\Get("/roles/{role_id}")
     * @ParamConverter("role", class="AppBundle:Role", options={"id"="role_id"})
     *
     * @param Role $role
     *
     * @Security("is_granted('edit')")
     *
     * @return View
     *
     * @throws EntityNotFoundException
     */
    public function readAction(Role $role = null)
    {
        if (null === $role) {
            throw new EntityNotFoundException();
        }

        return $this
            ->view($role, Response::HTTP_OK)
            ->setSerializationContext(
                SerializationContext::create()
                    ->setGroups(['Default', 'roles_read'])
            );
    }
}
