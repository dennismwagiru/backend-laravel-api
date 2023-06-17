<?php

namespace App\Transformers;

use App\Models\Article;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;
use League\Fractal\Resource\NullResource;
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
        'author', 'source', 'categories'
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
            'source_id' => $article->source_id,
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

    /**
     * @param Article $article
     * @return Item|NullResource
     */
    public function includeAuthor(Article $article): Item|NullResource
    {
        if (is_null($article->author)) {
            return $this->null();
        }
        return $this->item($article->author, new AuthorTransformer(), 'include');
    }

    /**
     * @param Article $article
     * @return Item|NullResource
     */
    public function includeSource(Article $article): Item|NullResource
    {
        if (is_null($article->source)) {
            return $this->null();
        }
        return $this->item($article->source, new SourceTransformer(), 'include');
    }

    /**
     * @param Article $article
     * @return Collection
     */
    public function includeCategories(Article $article): Collection
    {
        return $this->collection($article->categories, new CategoryTransformer(), 'include');
    }
}
