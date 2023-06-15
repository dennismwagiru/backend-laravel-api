<?php

namespace App\Transformers;

use App\Models\Author;
use League\Fractal\TransformerAbstract;

class AuthorTransformer extends TransformerAbstract
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
        //
    ];

    /**
     * A Fractal transformer.
     *
     * @param Author $author
     * @return array
     */
    public function transform(Author $author): array
    {
        return [
            'id' => $author->id,
            'source' => $author->source,
            'name' => $author->name,
            'description' => $author->description,
            'created_at' => $author->created_at
        ];
    }
}
