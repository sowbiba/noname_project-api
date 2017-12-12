<?php

namespace AppBundle\Exception;

class NotUpdatableVersionException extends \Exception
{
    protected $message = 'You can\'t update this version!';
}
