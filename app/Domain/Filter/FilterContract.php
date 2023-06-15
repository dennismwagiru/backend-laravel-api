<?php

namespace App\Domain\Filter;

interface FilterContract
{
    /**
     * @param string|bool|int|array $value
     * @return void
     */
    public function handle(string|bool|int|array $value): void;
}