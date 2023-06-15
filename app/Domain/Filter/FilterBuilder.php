<?php

namespace App\Domain\Filter;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class FilterBuilder
{

    protected Builder $query;
    protected array $filters;
    protected string $namespace;

    /**
     * @param Builder<Model> $query
     * @param array<string, string|bool|int> $filters
     * @param string $namespace
     */
    public function __construct(Builder $query, array $filters, string $namespace)
    {
        $this->query = $query;
        $this->filters = $filters;
        $this->namespace = $namespace;
    }

    /**
     * @return Builder
     */
    public function apply(): Builder
    {
        foreach ($this->filters as $name => $value) {
            $normalizedName = $this->normalizeName($name);
            $class = "{$this->namespace}\\{$normalizedName}";

            if (!class_exists($class)) {
                continue;
            }

            (new $class($this->query))->handle($value);
        }
        return $this->query;
    }

    /**
     * @param string $name
     * @return string
     */
    private function normalizeName(string $name): string
    {
        [$result] = explode("__", $name, 2);
        return implode('', array_map(function ($var) {
            return ucfirst($var);
        }, explode('_', $result)));
    }

}