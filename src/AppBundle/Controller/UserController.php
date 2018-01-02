<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
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
 * UserController is a RESTful controller managing users CRUD and listing.
 *
 * @Rest\NamePrefix("user_")
 */
class UserController extends ApiController
{
    /**
     * Gets the list of users.
     *
     * <hr>
     *
     * After getting the initial list, use the <strong>first, last, next, prev</strong> link relations in the
     * <strong>_links</strong> property to get more users in the list. Note that <strong>next</strong> will not be
     * available at the end of the list and <strong>prev</strong> will not be available at the start of the list. If
     * the results are exactly one page neither <strong>prev</strong> nor <strong>next</strong> will be available.
     *
     * The <strong>_embedded</strong> embedded user resources key'ed by relation name.
     *
     * <hr>
     *
     * The filters allows you to use the percent sign and underscore wildcards (e.g. name, %name, name%, %name%,
     * na_e, n%e).
     *
     * @ApiDoc(
     *     section="User",
     *     description="List users",
     *     statusCodes={
     *         200="OK",
     *         400="Bad request",
     *         403="Forbidden",
     *     },
     *     filters={
     *         {
     *             "name"="active",
     *             "dataType"="boolean",
     *             "description"="Is the user active ? Up to 1 character (0 or 1).",
     *             "required"=false,
     *         },
     *         {
     *             "name"="id",
     *             "dataType"="integer",
     *             "description"="ID for the user. Up to 11 characters.",
     *             "required"=false,
     *         },
     *         {
     *             "name"="firstname",
     *             "dataType"="string",
     *             "description"="Firstname of the user. Up to 255 characters.",
     *             "required"=false,
     *         },
     *         {
     *             "name"="lastname",
     *             "dataType"="string",
     *             "description"="Lastname of the user. Up to 255 characters.",
     *             "required"=false,
     *         },
     *         {
     *             "name"="email",
     *             "dataType"="string",
     *             "description"="Email of the user. Up to 255 characters.",
     *             "required"=false,
     *         },
     *         {
     *             "name"="username",
     *             "dataType"="string",
     *             "description"="Login of the user. Up to 255 characters.",
     *             "required"=false,
     *         },
     *         {
     *             "name"="role",
     *             "dataType"="integer",
     *             "description"="User's role id. Up to 11 digits.",
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
    names are id and name. Valid orders are asc and desc. e.g. If you want the users ordered by name in descending
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
     * @Rest\Get("/users")
     *
     * @ParamConverter("users", class="AppBundle:User", converter="collection_param_converter", options={"name"="users"})
     *
     * @param Request    $request
     * @param Collection    $users
     *
     * @Security("is_granted('edit')")
     *
     * @return View
     */
    public function listAction(Request $request, Collection $users)
    {
        if ('' !== $fields = $request->query->get('fields', '')) {
            $fields = array_merge(explode(',', $fields), ['users']);
        }

        return $this
            ->view($users, Response::HTTP_OK)
            ->setSerializationContext(
                SerializationContext::create()
                    ->setGroups(['Default', 'users_list'])
                    ->addExclusionStrategy(
                        new FieldsListExclusionStrategy('AppBundle\Entity\User', $fields)
                    )
            );
    }

    /**
     * @ApiDoc(
     *     section="User",
     *     description="Create new user",
     *     statusCodes={
     *         201="Created",
     *         400="Bad request",
     *         403="Forbidden",
     *         422="Validation failed",
     *     },
     *     parameters={
     *         {
     *             "name"="firstname",
     *             "dataType"="string",
     *             "description"="Firstname of the user. Up to 255 characters.",
     *             "required"=false,
     *         },
     *         {
     *             "name"="lastname",
     *             "dataType"="string",
     *             "description"="Lastname of the user. Up to 255 characters.",
     *             "required"=false,
     *         },
     *         {
     *             "name"="email",
     *             "dataType"="string",
     *             "description"="Email of the user. Up to 255 characters.",
     *             "required"=false,
     *         },
     *         {
     *             "name"="phone",
     *             "dataType"="string",
     *             "description"="Phone of the user. Up to 255 characters.",
     *             "required"=false,
     *         },
     *         {
     *             "name"="address",
     *             "dataType"="string",
     *             "description"="Address of the user. Up to 255 characters.",
     *             "required"=false,
     *         },
     *         {
     *             "name"="birthdate",
     *             "dataType"="string",
     *             "description"="Birthdate of the user. Up to 255 characters.",
     *             "required"=false,
     *         },
     *         {
     *             "name"="username",
     *             "dataType"="string",
     *             "description"="Login of the user. Up to 255 characters.",
     *             "required"=false,
     *         },
     *         {
     *             "name"="password",
     *             "dataType"="password",
     *             "description"="Password of the user. Up to 255 characters.",
     *             "required"=false,
     *         },
     *         {
     *             "name"="role",
     *             "dataType"="integer",
     *             "description"="User's role id. Up to 11 digits.",
     *             "required"=false,
     *         },
     *     },
     * )
     *
     * @Rest\Post("/users")
     *
     * @param Request $request
     *
     * @Security("is_granted('create')")
     *
     * @return View
     */
    public function createAction(Request $request)
    {
        $user = $this->get('app.manager.user')->createAndSave($request->request->all());

        return $this
            ->view($user, Response::HTTP_CREATED)
            ->setSerializationContext(
                SerializationContext::create()
                    ->setGroups(['Default', 'users_create'])
            );
    }


    /**
     * @ApiDoc(
     *     section="User",
     *     description="Retrieve a user",
     *     statusCodes={
     *         200="OK",
     *         403="Forbidden",
     *         404="Not found",
     *     },
     *     requirements={
     *         {
     *             "name"="user_id",
     *             "dataType"="integer",
     *             "description"="the user id",
     *         }
     *     },
     * )
     *
     * @Rest\Get("/users/{user_id}")
     * @ParamConverter("user", class="AppBundle:User", options={"id"="user_id"})
     *
     * @param User $user
     *
     * @Security("is_granted('view')")
     *
     * @return View
     *
     * @throws EntityNotFoundException
     */
    public function readAction(User $user = null)
    {
        if (null === $user) {
            throw new EntityNotFoundException();
        }

        return $this
            ->view($user, Response::HTTP_OK)
            ->setSerializationContext(
                SerializationContext::create()
                    ->setGroups(['Default', 'users_read'])
            );
    }

    /**
     * @ApiDoc(
     *     section="User",
     *     description="Update a user",
     *     statusCodes={
     *         200="OK",
     *         403="Forbidden",
     *         404="Not found",
     *         422="Validation failed",
     *     },
     *     requirements={
     *         {
     *             "name"="user_id",
     *             "dataType"="integer",
     *             "description"="the user id",
     *             "required"=true,
     *         }
     *     },
     *     parameters={
     *         {
     *             "name"="firstname",
     *             "dataType"="string",
     *             "description"="Firstname of the user. Up to 255 characters.",
     *             "required"=false,
     *         },
     *         {
     *             "name"="lastname",
     *             "dataType"="string",
     *             "description"="Lastname of the user. Up to 255 characters.",
     *             "required"=false,
     *         },
     *         {
     *             "name"="email",
     *             "dataType"="string",
     *             "description"="Email of the user. Up to 255 characters.",
     *             "required"=false,
     *         },
     *         {
     *             "name"="phone",
     *             "dataType"="string",
     *             "description"="Phone of the user. Up to 255 characters.",
     *             "required"=false,
     *         },
     *         {
     *             "name"="address",
     *             "dataType"="string",
     *             "description"="Address of the user. Up to 255 characters.",
     *             "required"=false,
     *         },
     *         {
     *             "name"="birthdate",
     *             "dataType"="integer",
     *             "description"="Birthdate of the user. Format dd/mm/yy.",
     *             "required"=false,
     *         },
     *         {
     *             "name"="username",
     *             "dataType"="string",
     *             "description"="Login of the user. Up to 255 characters.",
     *             "required"=false,
     *         },
     *         {
     *             "name"="password",
     *             "dataType"="password",
     *             "description"="Password of the user. Up to 255 characters.",
     *             "required"=false,
     *         },
     *         {
     *             "name"="role",
     *             "dataType"="integer",
     *             "description"="User's role id. Up to 11 digits.",
     *             "required"=false,
     *         },
     *     },
     * )
     *
     * @Rest\Put("/users/{user_id}")
     * @ParamConverter("user", class="AppBundle:User", options={"id"="user_id"})
     *
     * @param Request $request
     * @param User   $user
     *
     * @Security("is_granted('edit')")
     *
     * @return View
     *
     * @throws EntityNotFoundException
     */
    public function updateAction(Request $request, User $user = null)
    {
        if (null === $user) {
            throw new EntityNotFoundException();
        }

        $user = $this->get('app.manager.user')->updateAndSave($user, $request->request->all());

        return $this
            ->view($user, Response::HTTP_OK)
            ->setSerializationContext(
                SerializationContext::create()
                    ->setGroups(['Default', 'users_update'])
            );
    }

    /**
     * @ApiDoc(
     *     section="User",
     *     description="Delete a user",
     *     statusCodes={
     *         204="OK",
     *         403="Forbidden",
     *         404="Not found",
     *     },
     *     requirements={
     *         {
     *             "name"="user_id",
     *             "dataType"="integer",
     *             "description"="the user id",
     *         }
     *     },
     * )
     *
     * @Rest\Delete("/users/{user_id}")
     * @ParamConverter("user", class="AppBundle:User", options={"id"="user_id"})
     *
     * @param User $user
     *
     * @Security("is_granted('edit')")
     *
     * @return View
     *
     * @throws EntityNotFoundException
     */
    public function deleteAction(User $user = null)
    {
        if (null === $user) {
            throw new EntityNotFoundException();
        }

        $this->get('app.manager.user')->delete($user);

        return $this->view(null, Response::HTTP_NO_CONTENT);
    }
}
