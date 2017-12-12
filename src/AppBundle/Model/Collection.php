<?php

namespace AppBundle\Model;

class Collection extends \ArrayObject
{
    /**
     * @var string
     */
    private $name;

    /**
     * @param string            $name
     * @param array|null|object $input
     */
    public function __construct($name, $input)
    {
        $this->name = $name;

        parent::__construct($input);
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return (string) $this->name;
    }
}
