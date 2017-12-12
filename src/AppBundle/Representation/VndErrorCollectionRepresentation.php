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
 * @Hateoas\Relation("errors", embedded="expr(object.getErrors())")
 */
class VndErrorCollectionRepresentation
{
    /**
     * @Serializer\Expose
     */
    private $message;

    /**
     * @var array
     */
    private $errors;

    /**
     * @Serializer\Expose
     * @Serializer\XmlAttribute
     */
    private $logref;

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
     * @param array    $errors
     * @param null     $logref
     * @param Relation $help
     * @param Relation $describes
     */
    public function __construct($message, array $errors = [], $logref = null, Relation $help = null, Relation $describes = null)
    {
        $this->message = $message;
        $this->errors = $errors;
        $this->logref = $logref;
        $this->help = $help;
        $this->describes = $describes;
    }

    /**
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * @param                        $object
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
     */
    public function getLogref()
    {
        return $this->logref;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return [
            'message' => $this->getMessage(),
            'logref' => $this->getLogref(),
            '_embedded' => [
                'errors' => array_map(function ($error) {
                    return $error->toArray();
                }, $this->getErrors()),
            ],
        ];
    }
}
