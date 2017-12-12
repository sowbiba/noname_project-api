<?php

namespace AppBundle\Representation;

use Hateoas\Representation\RouteAwareRepresentation;
use JMS\Serializer\Annotation as Serializer;

/**
 * @Serializer\ExclusionPolicy("all")
 * @Serializer\XmlRoot("collection")
 * @Serializer\AccessorOrder("custom", custom = {"total"})
 */
class NotPaginatedRepresentation extends RouteAwareRepresentation
{
    /**
     * @var int
     *
     * @Serializer\Expose
     * @Serializer\Type("integer")
     * @Serializer\XmlAttribute
     */
    private $total;

    public function __construct(
        $inline,
        $route,
        array $parameters = array(),
        $absolute = false,
        $total = null
    ) {
        $this->total = $total;

        parent::__construct($inline, $route, $parameters, $absolute);
    }

    /**
     * @return int
     */
    public function getTotal()
    {
        return $this->total;
    }
}
