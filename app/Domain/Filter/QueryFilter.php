<?php

namespace App\Domain\Filter;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

abstract class QueryFilter
{
    /**
     * @var Builder<Model>
     */
    protected Builder $query;

    /**
     * @param Builder $query
     */
    public function __construct(Builder $query)
    {
        $this->query = $query;
    }

}