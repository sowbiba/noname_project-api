<?php

namespace AppBundle\Model;

trait Stateful
{
    protected $state;

    /**
     * @param string $state
     */
    public function setFiniteState($state)
    {
        $this->state = $state;
    }

    /**
     * @return string
     */
    public function getFiniteState()
    {
        return $this->state;
    }
}
