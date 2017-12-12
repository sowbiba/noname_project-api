<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use AppBundle\Exception\NotValidPasswordException;
use FOS\RestBundle\Context\Context;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use JMS\Serializer\SerializationContext;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;

/**
 * AuthenticationController is a RESTful controller managing user authentication.
 *
 * @Rest\NamePrefix("authentication_")
 */
class AuthenticationController extends FOSRestController
{
    /**
     * Checks if the combination of login and password is valid:
     * - if true, it generate a new token, save it in database and returns an array
     * which contains user data.
     *
     * @example
     * [
     *      'email'     => 'email@profideo.com',
     *      'firstname' => 'Firstname',
     *      'id'        => 1,
     *      'name'      => 'NAME',
     *      'roles'     => [
     *          'id'   => 1,
     *          'name' => 'role',
     *      ],
     *      'token'     => 'aRandomlyGeneratedToken',
     *      'username'  => 'username',
     * ]
     *
     * - if false, it returns:
     *      - a "401 Unauthorized" HTTP error if the combination login/password is not valid.
     *      - a "404 Not Found" HTTP error if the username is invalid
     *
     * @ApiDoc(
     *     section="Authentication",
     *     description="Returns user data if the given login/password combination is valid",
     *     statusCodes={
     *         200="OK",
     *         401="Unauthorized",
     *         404="Not found",
     *     },
     *     parameters={
     *         {
     *             "name"="login",
     *             "dataType"="string",
     *             "required"=false,
     *         },
     *         {
     *             "name"="password",
     *             "dataType"="string",
     *             "required"=false,
     *         },
     *     },
     * )
     *
     * @Rest\Post("/login-check")
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function loginCheckAction(Request $request)
    {
        try {
            $user = $this->get('app.manager.user')->loadUserByCredentials(
                $request->request->get('login'),
                $request->request->get('password'),
                $this->get('app.security.token_policy')
            );

            return $this->view($user, Response::HTTP_OK)
                        ->setSerializationContext(
                            SerializationContext::create()
                                ->setGroups(['Default', 'authentication'])
                        )
                ;
        } catch (NotValidPasswordException $e) {
            return new JsonResponse('', Response::HTTP_UNAUTHORIZED);
        } catch (UsernameNotFoundException $e) {
            return new JsonResponse('Username not found', Response::HTTP_NOT_FOUND);
        }
    }

    /**
     * @ApiDoc(
     *     section="Authentication",
     *     description="Returns user data",
     *     statusCodes={
     *         200="Returned when successful",
     *     },
     * )
     *
     * @Rest\Route("/me", methods={"GET", "POST"})
     *
     * @return JsonResponse
     */
    public function meAction()
    {
        $user = $this->getUser();
        if (!$user instanceof User) {
            return new JsonResponse('User not found', Response::HTTP_NOT_FOUND);
        }

        return $this->view($user, Response::HTTP_OK)
            ->setSerializationContext(
                SerializationContext::create()
                    ->setGroups(['Default', 'authentication'])
            )
            ;
    }
}
