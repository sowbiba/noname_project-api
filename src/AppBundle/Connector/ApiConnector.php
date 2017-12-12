<?php

namespace AppBundle\Connector;

use Symfony\Component\HttpFoundation\Response;
use GuzzleHttp\Client;
use Symfony\Component\DependencyInjection\ContainerInterface;

class ApiConnector
{
    const TOKEN_NAME = 'apptoken';

    const HTTP_METHOD_DELETE = 'delete';
    const HTTP_METHOD_GET = 'get';
    const HTTP_METHOD_HEAD = 'head';
    const HTTP_METHOD_OPTIONS = 'options';
    const HTTP_METHOD_PATCH = 'patch';
    const HTTP_METHOD_POST = 'post';
    const HTTP_METHOD_PUT = 'put';
    /**
     * @var Client
     */
    protected $client;
    /**
     * @var ContainerInterface
     */
    protected $container;
    /**
     * @param Client             $client
     * @param ContainerInterface $container
     */
    public function __construct(Client $client, ContainerInterface $container)
    {
        $this->client = $client;
        $this->container = $container;
    }

    /**
     * @param string $httpMethod
     * @param string $url
     * @param array  $options
     *
     * @return mixed
     */
    public function request($httpMethod, $url, array $options = [])
    {
        $response = $this->requestApi($httpMethod, $url, $options);

        if ('application/json' === $response->getHeader('Content-Type') || Response::HTTP_NO_CONTENT === $response->getStatusCode()) {
            return $response->json();
        }

        return $response;
    }

    /**
     * @param $httpMethod
     * @param $url
     * @param array $options
     *
     * @return \GuzzleHttp\Message\FutureResponse|\GuzzleHttp\Message\ResponseInterface|\GuzzleHttp\Ring\Future\FutureInterface|null
     */
    public function requestAndGetResponse($httpMethod, $url, array $options = [])
    {
        return $this->requestApi($httpMethod, $url, $options);
    }

    private function requestApi($httpMethod, $url, array $options = [])
    {
        return $this->client->send($this->client->createRequest($httpMethod, ltrim($url, '/'), $this->getOptions($options)));
    }

    /**
     * @param array $options
     *
     * @return array
     */
    private function getOptions(array $options = [])
    {
        $apiKeyOption = ['apptoken' => $this->getUser() ? $this->getUser()->getToken() : null];

        if (isset($options['headers'])) {
            if (!array_key_exists('apptoken', $options['headers'])) {
                $options['headers'] = array_merge($options['headers'], $apiKeyOption);
            }
        } else {
            $options = array_merge(
                $options,
                [
                    'headers' => $apiKeyOption,
                ]
            );
        }

        return $options;
    }

    /**
     * Get a user from the Security Token Storage.
     *
     * @return mixed
     *
     * @throws \LogicException If SecurityBundle is not available
     *
     * @see TokenInterface::getUser()
     */
    public function getUser()
    {
        if (!$this->container->has('security.token_storage')) {
            throw new \LogicException('The SecurityBundle is not registered in your application.');
        }

        if (null === $token = $this->container->get('security.token_storage')->getToken()) {
            return;
        }

        if (!is_object($user = $token->getUser())) {
            // e.g. anonymous authentication
            return;
        }

        return $user;
    }
}