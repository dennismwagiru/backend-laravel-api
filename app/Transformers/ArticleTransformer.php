<?php

namespace App\Transformers;

use App\Models\Article;
use League\Fractal\TransformerAbstract;

class ArticleTransformer extends TransformerAbstract
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
     * @param Article $article
     * @return array
     */
    public function transform(Article $article): array
    {
        return [
            'id' => $article->id,
            'author_id' => $article->author_id,
            'title' => $article->title,
            'web_url' => $article->web_url,
            'image_url' => $article->image_url,
            'description' => $article->description,
            'content' => $article->content,
            'published_at' => $article->published_at,
            'created_at' => $article->created_at
        ];
    }
}
