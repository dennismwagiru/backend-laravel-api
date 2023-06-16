<?php

namespace App\Domain\News;

use App\Models\Article;
use App\Models\Author;
use App\Models\Category;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;

class NewsApiService implements ApiService
{

    const BASE_URL = 'https://newsapi.org/v2/';
    const KEY = 'news-api';

    /**
     * @return string[]
     */
    public function getHeaders(): array
    {
        return [
            'Accept' => 'application/json',
            'Authorization' => "Bearer " . config('settings.sources.news-api.api-key')
        ];
    }

    /**
     * @return void
     */
    public function articles(): void
    {
        $categories = $this->categories();

        foreach ($categories as $category) {
            $this->articlesByCategory($category);
        }

    }

    /**
     * @return string[]
     */
    public function categories(): array
    {
        return [
            'business', 'entertainment', 'general', 'health', 'science', 'sports', 'technology'
        ];
    }

    /**
     * @param string $category
     * @return void
     */
    public function articlesByCategory(string $category): void
    {
        $response = Http::withHeaders($this->getHeaders())
            ->get(self::BASE_URL . 'top-headlines', 'language=en&category=' . $category);

        if ($response->successful())
            $this->saveArticles($response->json(['articles']), $category);
    }

    /**
     * @param string $author
     * @return void
     */
    public function articlesByAuthor(string $author): void
    { }

    /**
     * @param array $articles
     * @param string|null $category
     * @return void
     */
    public function saveArticles(array $articles = [], string $category = null): void
    {
        foreach ($articles as $article) {
            \Log::info($article);
            $author = null;
            if (!is_null($article['author'])) {
                $author = Author::firstOrCreate([
                    'name' => $article['author'],
                    'source' => 'news-api'
                ]);
            }

            $saved = Article::updateOrCreate(
                [
                    'author_id' => $author?->id,
                    'title' => $article['title'],
                ], [
                    'web_url' => $article['url'],
                    'image_url' => $article['urlToImage'],
                    'description' => $article['description'],
                    'content' => $article['content'],
                    'published_at' => is_null($article['publishedAt']) ? now() : Carbon::parse($article['publishedAt'])
                ]
            );

            $cat = Category::where('name', $category)
                ->first();
            if (is_null($cat)) {
                $cat = Category::create(['name' => $category, 'sources' => [self::KEY]]);
            } elseif (!in_array(self::KEY, $cat->sources)) {
                $cat->sources = [ ...$cat->sources, self::KEY];
            }

            $saved->categories()->sync([$cat->id]);
        }

    }
}