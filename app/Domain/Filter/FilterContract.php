<?php

namespace App\Domain\Filter;

interface FilterContract
{
    public function handle(string|bool|int $value): void;
}