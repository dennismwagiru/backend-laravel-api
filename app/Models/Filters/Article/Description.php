<?php

namespace App\Models\Filters\Article;

use App\Domain\Filter\FilterContract;
use App\Domain\Filter\QueryFilter;

class Description extends QueryFilter implements FilterContract
{
    /**
     * @param bool|int|string|array $value
     * @return void
     */
    public function handle(bool|int|string|array $value): void
    {
        $this->query->where('description', 'like', '%'. $value .'%');
    }

}