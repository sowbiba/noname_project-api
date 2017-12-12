<?php

namespace AppBundle\Model;

interface PaginatableInterface
{
    const DEFAULT_NUM_ITEMS = 20;

    /**
     * @return int
     */
    public function getDefaultPaginationNumItems();
}
