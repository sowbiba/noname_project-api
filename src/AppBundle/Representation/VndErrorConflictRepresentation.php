<?php

namespace AppBundle\Representation;

use JMS\Serializer\Annotation as Serializer;

/**
 * @Serializer\ExclusionPolicy("all")
 * @Serializer\XmlRoot("resource")
 */
class VndErrorConflictRepresentation
{
    /**
     * @Serializer\Expose
     */
    private $field;

    /**
     * @Serializer\Expose
     */
    private $currentValue;

    /**
     * @Serializer\Expose
     */
    private $submittedValue;

    /**
     * @param string $field
     * @param mixed  $currentValue
     * @param mixed  $submittedValue
     */
    public function __construct($field, $currentValue, $submittedValue)
    {
        $this->field = $field;
        $this->currentValue = $currentValue;
        $this->submittedValue = $submittedValue;
    }

    /**
     * @return string
     */
    public function getField()
    {
        return $this->field;
    }

    /**
     * @return mixed
     */
    public function getCurrentValue()
    {
        return $this->currentValue;
    }

    /**
     * @return mixed
     */
    public function getSubmittedValue()
    {
        return $this->submittedValue;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return [
            'field' => $this->getField(),
            'current_value' => $this->getCurrentValue(),
            'submitted_value' => $this->getSubmittedValue(),
        ];
    }
}
