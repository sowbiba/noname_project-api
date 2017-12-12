<?php

namespace AppBundle\Model;

use Doctrine\Common\Collections\Criteria;

interface FilterableInterface
{
    const FILTER_ORDER_ASC = Criteria::ASC;
    const FILTER_ORDER_DESC = Criteria::DESC;

    const DEFAULT_FILTER_FIELD_ORDER = 'id';
    const DEFAULT_FILTER_ORDER = self::FILTER_ORDER_ASC;

    /**
     * Returns array of current entity fields name that can be filtered. Fields name can be one of an other entity
     * linked to the current one.
     *
     * Note : If the filter is on a relationship
     *        then you must specify the type of join (cf: See the example below)
     *
     * Example data to return
     * [
     *   'id' => ['field' => 'id'],
     *   'username' => ['field' => 'username'],
     *   'name' => ['field' => 'userProfile.name', 'join' => 'join'],
     *   'roles' => ['field' => 'roles.description', 'join' => 'leftJoin'],
     * ];
     *
     * @return array
     */
    public static function getFiltersMapping();
}
