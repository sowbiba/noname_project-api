<?php

namespace AppBundle\Exception;

use GuzzleHttp\Exception\RequestException;
use Symfony\Component\HttpKernel\Exception\HttpException;

class ApiRequestException extends HttpException
{
    /**
     * @param RequestException $exception The request exception
     */
    public function __construct(RequestException $exception)
    {
        $response = $exception->getResponse();

        parent::__construct($response->getStatusCode(), $response->json()['message'], $exception, [], 0);
    }
}