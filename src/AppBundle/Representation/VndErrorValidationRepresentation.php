<?php

namespace AppBundle\Representation;

use Hateoas\Configuration\Relation;
use Hateoas\Configuration\Annotation as Hateoas;
use Hateoas\Configuration\Metadata\ClassMetadataInterface;
use JMS\Serializer\Annotation as Serializer;

/**
 * @Serializer\ExclusionPolicy("all")
 * @Serializer\XmlRoot("resource")
 *
 * @Hateoas\RelationProvider("getRelations")
 */
class VndErrorValidationRepresentation
{
    /**
     * @Serializer\Expose
     */
    private $message;

    /**
     * @Serializer\Expose
     */
    private $ref;

    /**
     * @Serializer\Expose
     */
    private $code;

    /**
     * @Serializer\Expose
     */
    private $field;

    /**
     * @var Relation
     */
    private $help;

    /**
     * @var Relation
     */
    private $describes;

    /**
     * @param $message
     * @param $ref
     * @param $code
     * @param null     $field
     * @param Relation $help
     * @param Relation $describes
     */
    public function __construct($message, $ref, $code, $field = null, Relation $help = null, Relation $describes = null)
    {
        $this->message = $message;
        $this->ref = $ref;
        $this->code = $code;
        $this->field = $field;
        $this->help = $help;
        $this->describes = $describes;
    }

    /**
     * @param $object
     * @param ClassMetadataInterface $classMetadata
     *
     * @return array
     */
    public function getRelations($object, ClassMetadataInterface $classMetadata)
    {
        $relations = array();

        if (null !== $this->help) {
            $relations[] = $this->help;
        }

        if (null !== $this->describes) {
            $relations[] = $this->describes;
        }

        return $relations;
    }

    /**
     * @return mixed
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @return mixed
     */
    public function getRef()
    {
        return $this->ref;
    }

    /**
     * @return mixed
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @return null|string
     */
    public function getField()
    {
        return $this->field;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return [
            'message' => $this->getMessage(),
            'ref' => $this->getRef(),
            'code' => $this->getCode(),
            'field' => $this->getField(),
            'help' => null, // todo
            'describes' => null, // todo
        ];
    }
}
