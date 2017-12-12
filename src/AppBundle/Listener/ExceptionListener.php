<?php

namespace AppBundle\Listener;

use AppBundle\Exception\EntityLockedException;
use AppBundle\Exception\EntityNotFoundException;
use AppBundle\Exception\NotValidFormException;
use AppBundle\Representation\VndErrorCollectionRepresentation;
use AppBundle\Representation\VndErrorConflictRepresentation;
use AppBundle\Representation\VndErrorRepresentation;
use AppBundle\Representation\VndErrorValidationRepresentation;
use AppBundle\Serializer\FormErrorsSerializer;
use AppBundle\Utils\HashGenerator;
use AppBundle\Utils\Inflector;
use FOS\RestBundle\Context\Context;
use FOS\RestBundle\View\View;
use JMS\Serializer\SerializationContext;
use Monolog\Logger;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Security\Core\Exception\InsufficientAuthenticationException;

class ExceptionListener
{
    const LOG_PREFIX = 'no_name_api';

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @param GetResponseForExceptionEvent $event
     */
    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        $exception = $event->getException();
        $logRef = $this->getLogRefPrefix($event->getRequest());

        switch (true) {
            case $exception instanceof NotValidFormException:
                $view = $this->handleNotValidFormException($exception, $logRef);
                break;
            case $exception instanceof EntityNotFoundException:
                $view = $this->handleEntityNotFoundException($exception, $logRef);
                break;
            case $exception instanceof EntityLockedException:
                $view = $this->handleEntityLockedException($exception, $logRef);
                break;
            case $exception instanceof InsufficientAuthenticationException:
                $view = $this->handleInsufficientAuthenticationException($exception, $logRef);
                break;
            case $exception instanceof AccessDeniedHttpException:
                $view = $this->handleAccessDeniedHttpException($exception, $logRef);
                break;
            case $exception instanceof HttpException:
                $view = $this->handleHttpException($exception, $logRef);
                break;
            default:
                $view = $this->handleException($exception, $logRef);
        }

        $event->setResponse(
            $this->container->get('fos_rest.view_handler')->handle($view, $event->getRequest())
        );
    }

    /**
     * Returns the log ref prefix depending of the request (_controller request parameter).
     * e.g. for MyTestController::superTestAction -> my_test.super_test.
     *
     * @param Request $request
     *
     * @return string
     */
    private function getLogRefPrefix(Request $request)
    {
        $logPrefix = 'application';

        if (null !== $requestController = $request->get('_controller')) {
            $requestController = explode('::', $requestController);

            if (isset($requestController[0]) && isset($requestController[1])) {
                $controller = Inflector::underscore(str_replace('Controller', '', substr($requestController[0], strrpos($requestController[0], '\\') + 1)));
                $action = Inflector::underscore(str_replace('Action', '', $requestController[1]));

                $logPrefix = "$controller.$action";
            }
        }

        return $logPrefix;
    }

    /**
     * @param \Exception $exception
     * @param string     $logRef
     *
     * @return View
     */
    private function handleException(\Exception $exception, $logRef)
    {
        $statusCode = Response::HTTP_INTERNAL_SERVER_ERROR;

        $logRef = static::LOG_PREFIX.'.exception.'.$logRef.'.'.HashGenerator::generate();

        if ('dev' !== $this->container->get('kernel')->getEnvironment()) {
            $message = 'Internal Server Error';
        } else {
            $message = $this->container->get('translator')->trans($exception->getMessage(), [], 'error');
        }

        $errorMessage = new VndErrorRepresentation($statusCode, $message, $logRef);

        $this->log($exception, Logger::WARNING, 'exception', $logRef, $statusCode, $errorMessage->toArray());

        return View::create($errorMessage, $statusCode)
            ->setFormat('json')
            ->setSerializationContext(
                SerializationContext::create()->setSerializeNull(false)
            );
    }

    private function handleAccessDeniedHttpException(AccessDeniedHttpException $exception, $logRef)
    {
        $statusCode = $exception->getStatusCode();

        $logRef = static::LOG_PREFIX.'.access_denied.'.$logRef.'.'.HashGenerator::generate();
        $message = 'You do not have the necessary permissions.';

        $errorMessage = new VndErrorRepresentation($statusCode, $message, $logRef);

        $this->log($exception, Logger::WARNING, 'exception', $logRef, $statusCode, $errorMessage->toArray());

        return View::create($errorMessage, $statusCode)
            ->setFormat('json')
            ->setSerializationContext(
                SerializationContext::create()->setSerializeNull(false)
            );
    }

    private function handleInsufficientAuthenticationException(InsufficientAuthenticationException $exception, $logRef)
    {
        $statusCode = 403;

        $logRef = static::LOG_PREFIX.'.insufficient_authentication.'.$logRef.'.'.HashGenerator::generate();

        $errorMessage = new VndErrorRepresentation($statusCode, $exception->getMessage(), $logRef);

        $this->log($exception, Logger::WARNING, 'exception', $logRef, $statusCode, $errorMessage->toArray());

        return View::create($errorMessage, $statusCode)
            ->setFormat('json')
            ->setSerializationContext(
                SerializationContext::create()->setSerializeNull(false)
            );
    }

    /**
     * @param HttpException $exception
     * @param string        $logRef
     *
     * @return View
     */
    private function handleHttpException(HttpException $exception, $logRef)
    {
        $statusCode = $exception->getStatusCode();

        $statusText = 'http_exception';
        if (isset(Response::$statusTexts[$statusCode])) {
            $statusText = Response::$statusTexts[$statusCode];
            $statusText = strtolower($statusText);
            $statusText = str_replace('"', '', $statusText);
            $statusText = str_replace("'", '', $statusText);
            $statusText = str_replace(' ', '_', $statusText);
        }

        $logRef = static::LOG_PREFIX.".$statusText.".$logRef.'.'.HashGenerator::generate();
        $message = $this->container->get('translator')->trans($exception->getMessage(), [], 'error');

        $errorMessage = new VndErrorRepresentation($statusCode, $message, $logRef);

        $this->log($exception, Logger::WARNING, $statusText, $logRef, $statusCode, $errorMessage->toArray());

        return View::create($errorMessage, $statusCode)
            ->setFormat('json')
            ->setSerializationContext(
                SerializationContext::create()->setSerializeNull(false)
            );
    }

    /**
     * @param EntityNotFoundException $exception
     * @param string                  $logRef
     *
     * @return View
     */
    private function handleEntityNotFoundException(EntityNotFoundException $exception, $logRef)
    {
        $statusCode = Response::HTTP_NOT_FOUND;

        $logRef = static::LOG_PREFIX.'.entity_not_found.'.$logRef.'.'.HashGenerator::generate();
        $errorMessage = new VndErrorRepresentation(
            $statusCode,
            $this->container->get('translator')->trans('Not found', [], 'error'),
            $logRef
        );

        $this->log($exception, Logger::WARNING, 'entity_not_found', $logRef, $statusCode, $errorMessage->toArray());

        return View::create($errorMessage, $statusCode);
    }

    /**
     * @param EntityLockedException $exception
     * @param string                $logRef
     *
     * @return View
     */
    private function handleEntityLockedException(EntityLockedException $exception, $logRef)
    {
        $statusCode = Response::HTTP_LOCKED;

        $logRef = static::LOG_PREFIX.'.entity_locked.'.$logRef.'.'.HashGenerator::generate();
        $errorMessage = new VndErrorRepresentation(
            $statusCode,
            $this->container->get('translator')->trans('Locked', [], 'error'),
            $logRef
        );

        $this->log($exception, Logger::WARNING, 'entity_locked', $logRef, $statusCode, $errorMessage->toArray());

        return View::create($errorMessage, $statusCode);
    }

    /**
     * @param NotValidFormException $exception
     * @param string                $logRef
     *
     * @return View
     */
    private function handleNotValidFormException(NotValidFormException $exception, $logRef)
    {
        $statusCode = Response::HTTP_UNPROCESSABLE_ENTITY;

        $form = $exception->getForm();

        $logRef = static::LOG_PREFIX.'.validation.'.$logRef.'.'.HashGenerator::generate();
        $errorMessage = $this->getFormErrors($form, $logRef);

        $extra = [];
        foreach ($form->all() as $fieldName => $field) {
            $extra['formData'][$fieldName] = $field->getData();
        }

        $this->log($exception, Logger::INFO, 'validation', $logRef, $statusCode, $errorMessage->toArray(), $extra);

        return View::create($errorMessage, $statusCode)
            ->setFormat('json')
            ->setSerializationContext(
                SerializationContext::create()->setSerializeNull(false)
            );
    }

    /**
     * @param Form   $form
     * @param string $logRef
     *
     * @return VndErrorCollectionRepresentation
     */
    private function getFormErrors(Form $form, $logRef)
    {
        $formErrors = FormErrorsSerializer::serializeFormErrors($form);

        $translator = $this->container->get('translator');

        // The translation file should be define by following this naming convention : 'entity', 'entity_category'.
        $dataClass = $form->getConfig()->getDataClass();

        $translationDomain = null;
        if (null !== $dataClass) {
            $translationDomain = Inflector::underscore(substr($dataClass, strrpos($dataClass, '\\') + 1));
        }

        $errors = [];
        foreach ($formErrors['global'] as $error) {
            $message = $translator->trans($error['message'], [], $translationDomain);
            if ($message === $error['message']) {
                $message = $translator->trans($error['message'], [], 'error');
            }

            $errors[] = new VndErrorValidationRepresentation($message, $error['message'], $error['code'], null);
        }

        foreach ($formErrors['fields'] as $field => $error) {
            $message = $translator->trans($error['message'], [], $translationDomain);
            if ($message === $error['message']) {
                $message = $translator->trans($error['message'], [], 'error');
            }

            $errors[] = new VndErrorValidationRepresentation($message, $error['message'], $error['code'], $field);
        }

        return new VndErrorCollectionRepresentation(
            $this->container->get('translator')->trans('Validation failed', [], 'error'), $errors, $logRef
        );
    }

    /**
     * @param array  $differences
     * @param string $logRef
     *
     * @return VndErrorCollectionRepresentation
     */
    private function getConflictErrors(array $differences, $logRef)
    {
        $errors = [];
        foreach ($differences as $field => $diffs) {
            $errors[] = new VndErrorConflictRepresentation($field, $diffs[0], $diffs[1]);
        }

        return new VndErrorCollectionRepresentation(
            $this->container->get('translator')->trans('Conflicts', [], 'error'), $errors, $logRef
        );
    }

    /**
     * @param \Exception $exception
     * @param string     $recordType
     * @param string     $type
     * @param string     $logRef
     * @param int        $statusCode
     * @param array      $errorMessages
     * @param array      $extra
     */
    private function log(\Exception $exception, $recordType, $type, $logRef, $statusCode, array $errorMessages, array $extra = [])
    {
        $user = null;
        if (null !== $token = $this->container->get('security.token_storage')->getToken()) {
            $user = $token->getUser();
        }

        $request = $this->container->get('request_stack')->getCurrentRequest();
        $extra = array_merge($extra, [
            'trace' => $this->getExceptionTrace($exception),
            'uri' => $request->getUri(),
            'method' => $request->getMethod(),
            'route_name' => $request->get('_route'),
        ]);

        $userId = "NULL";
        if (is_object($user)) {
            $userId = $user->getId();
        } elseif (is_string($user)) {
            $userId = $user;
        }

        $this->container->get('app.logger')->addRecord(
            $recordType,
            json_encode(
                [
                    'ref' => $logRef,
                    'type' => $type,
                    'status_code' => $statusCode,
                    'referer' => $request->headers->get('referer'),
                    'user' => $userId,
                    'error_message' => $errorMessages,
                    'extra' => $extra,
                ]
            )
        );
    }

    /**
     * @param \Exception $exception
     *
     * @return array
     */
    private function getExceptionTrace(\Exception $exception)
    {
        return array_values(array_filter(explode('#', $exception->getTraceAsString())));
    }
}