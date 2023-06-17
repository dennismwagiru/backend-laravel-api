<?php

namespace App\Transformers;

use App\Models\Category;
use League\Fractal\Resource\Collection;
use League\Fractal\TransformerAbstract;

class CategoryTransformer extends TransformerAbstract
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
        'articles', 'sources'
    ];

    /**
     * A Fractal transformer.
     *
     * @param Category $category
     * @return array
     */
    public function transform(Category $category): array
    {
        return [
            'id' => $category->id,
            'name' => $category->name,
            'description' => $category->description,
            'created_at' => $category->created_at,
            'updated_at' => $category->updated_at,
        ];
    }

    /**
     * @param Category $category
     * @return Collection
     */
    public function includeArticles(Category $category): Collection
    {
        return $this->collection($category->articles, new SourceTransformer(), 'include');
    }

    /**
     * @param Category $category
     * @return Collection
     */
    public function includeSources(Category $category): Collection
    {
        return $this->collection($category->sources, new SourceTransformer(), 'include');
    }
}
