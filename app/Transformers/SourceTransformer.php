<?php

namespace App\Transformers;

use App\Models\Source;
use League\Fractal\Resource\Collection;
use League\Fractal\TransformerAbstract;

class SourceTransformer extends TransformerAbstract
{
    /**
     * List of resources to automatically include
     *
     * @var array
     */
    protected array $defaultIncludes = [
        //
    ];
    
    /**
     * List of resources possible to include
     *
     * @var array
     */
    protected array $availableIncludes = [
        'authors', 'categories'
    ];

    /**
     * A Fractal transformer.
     *
     * @param Source $source
     * @return array
     */
    public function transform(Source $source): array
    {
        return [
            'id' => $source->id,
            'name' => $source->api_key,
            'description' => $source->description,
            'created_at' => $source->created_at
        ];
    }

    /**
     * @param Source $source
     * @return Collection
     */
    public function includeAuthors(Source $source): Collection
    {
        return $this->collection($source->authors, new CategoryTransformer(), 'include');
    }

    /**
     * @param Source $source
     * @return Collection
     */
    public function includeCategories(Source $source): Collection
    {
        return $this->collection($source->categories, new CategoryTransformer(), 'include');
    }
}
