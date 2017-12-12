<?php

namespace AppBundle\Exception;

/**
 * Thrown to handle workflow not valid properties (releaseName).
 */
class NotValidWorkflowPropertiesException extends \Exception
{
    /**
     * @var array
     */
    protected $fields;

    /**
     * @param array $fields
     */
    public function __construct(array $fields)
    {
        $this->fields = $fields;
    }

    /**
     * @return array
     */
    public function getFields()
    {
        return $this->fields;
    }
}
