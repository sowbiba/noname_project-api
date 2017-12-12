<?php

namespace AppBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;

abstract class ApiController extends FOSRestController
{
    /**
     * @return mixed
     */
    public function getUser()
    {
        return $this->get('security.token_storage')->getToken()->getUser();
    }
}
