<?php

namespace AppBundle\Representation;

use Hateoas\Configuration\Relation;
use Hateoas\Representation\VndErrorRepresentation as HateoasVndErrorRepresentation;
use JMS\Serializer\Annotation as Serializer;

/**
 * @Serializer\ExclusionPolicy("all")
 * @Serializer\AccessorOrder("alphabetical")
 */
class VndErrorRepresentation extends HateoasVndErrorRepresentation
{
    /**
     * @var int
     *
     * @Serializer\Expose
     * @Serializer\SerializedName("status")
     * @Serializer\XmlAttribute
     */
    protected $statusCode;

    /**
     * @param string        $statusCode
     * @param int|null      $message
     * @param null          $logref
     * @param Relation|null $help
     * @param Relation|null $describes
     */
    public function __construct($statusCode, $message, $logref = null, Relation $help = null, Relation $describes = null)
    {
        parent::__construct($message, $logref, $help, $describes);

        $this->statusCode = $statusCode;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return [
            'message' => $this->getMessage(),
            'ref' => $this->getLogref(),
            'help' => null, // todo
            'describes' => null, // todo
        ];
    }
}
