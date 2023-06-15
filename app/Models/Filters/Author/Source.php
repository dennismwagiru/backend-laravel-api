<?php

namespace App\Models\Filters\Author;

use App\Domain\Filter\FilterContract;
use App\Domain\Filter\QueryFilter;

class Source extends QueryFilter implements FilterContract
{
    /**
     * @param bool|int|string|array $value
     * @return void
     */
    public function handle(bool|int|string|array $value): void
    {
        $this->query->where('source', $value);
    }

}