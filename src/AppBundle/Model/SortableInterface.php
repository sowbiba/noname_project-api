<?php

namespace AppBundle\Model;

use Doctrine\Common\Collections\Criteria;

interface SortableInterface
{
    const SORT_ORDER_ASC = Criteria::ASC;
    const SORT_ORDER_DESC = Criteria::DESC;

    /**
     * @return array
     */
    public static function getDefaultSortOrder();

    /**
     * Returns array of current entity fields name that can be ordered. Fields name can be one of an other entity
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
    public static function getOrdersMapping();
}
