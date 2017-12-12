<?php

namespace AppBundle\Serializer\Exclusion;

use JMS\Serializer\Exclusion\ExclusionStrategyInterface;
use JMS\Serializer\Metadata\ClassMetadata;
use JMS\Serializer\Metadata\PropertyMetadata;
use JMS\Serializer\Context;

/**
 * This exclusion strategy allow to serialize specific fields (which are not already excluded).
 *
 * @see http://jolicode.com/blog/how-to-implement-your-own-fields-inclusion-rules-with-jms-serializer
 */
class FieldsListExclusionStrategy implements ExclusionStrategyInterface
{
    /**
     * @var string
     */
    private $className;

    /**
     * @var array
     */
    private $fields = [];

    /**
     * @param string       $className
     * @param array|string $fields
     */
    public function __construct($className, $fields = [])
    {
        $this->className = $className;

        if (!is_array($fields)) {
            $fields = explode(',', $fields);
        }

        $this->fields = array_filter(array_map('trim', $fields));
    }

    /**
     * Whether the class should be skipped.
     *
     * @param ClassMetadata $metadata
     * @param Context       $navigatorContext
     *
     * @return bool
     */
    public function shouldSkipClass(ClassMetadata $metadata, Context $navigatorContext)
    {
        return false;
    }

    /**
     * Whether the property should be skipped.
     *
     * @param PropertyMetadata $property
     * @param Context          $navigatorContext
     *
     * @return bool
     */
    public function shouldSkipProperty(PropertyMetadata $property, Context $navigatorContext)
    {
        if (empty($this->fields)) {
            return false;
        }

        if ($this->className !== $property->class) {
            $type = isset($property->type['name']) ? $property->type['name'] : null;

            if ('Hateoas\Configuration\Relation' !== $property->class || 'Hateoas\Model\Embedded' !== $type) {
                return false;
            }
        }

        // Special key to avoid exposing class properties.
        if (in_array('none', $this->fields, true)) {
            return true;
        }

        return !in_array($property->serializedName ?: $property->name, $this->fields);
    }
}
