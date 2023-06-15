<?php

namespace App\Models\Filters\Category;

use App\Domain\Filter\FilterContract;
use App\Domain\Filter\QueryFilter;

class Query extends QueryFilter implements FilterContract
{
    /**
     * @param bool|int|string $value
     * @return void
     */
    public function handle(bool|int|string|array $value): void
    {
        $this->query->where('name', 'like', '%'. $value .'%');
    }

}