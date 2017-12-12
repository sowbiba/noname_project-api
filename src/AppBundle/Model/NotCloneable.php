<?php

namespace AppBundle\Model;

trait NotCloneable
{
    protected function __clone()
    {
    }
}
