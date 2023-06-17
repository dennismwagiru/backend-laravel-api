<?php

namespace App\Domain\News;

use App\Models\Source;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;

class NewYorkTimesApiService implements ApiService
{

    const BASE_URL = 'https://api.nytimes.com/svc/topstories/v2/';
    protected Source $source;

    /**
     * @param Source $source
     */
    public function setSource(Source $source): void
    {
        $this->source = $source;
    }

    /**
     * @return string[]
     */
    public function getHeaders(): array
    {
        return [
            'Accept' => 'application/json',
            'Authorization' => "Bearer " . $this->source->api_key
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
            'arts', 'automobiles', 'books/review', 'business', 'fashion', 'food', 'health', 'home', 'insider',
            'magazine', 'movies', 'nyregion', 'obituaries', 'opinion', 'politics', 'realestate', 'science',
            'sports', 'sundayreview', 'technology', 'theater', 't-magazine', 'travel', 'upshot', 'us', 'world'
        ];
    }

    /**
     * @param string $category
     * @return void
     */
    public function articlesByCategory(string $category): void
    {
        $response = Http::withHeaders($this->getHeaders())
            ->get(self::BASE_URL . strtolower($category) . '.json', 'api-key=' .$this->source->api_key );

        if ($response->successful() && strtolower($response->json('status')) == 'ok')
            $this->saveArticles($response->json('results'));
    }

    /**
     * @param string $author
     * @return void
     */
    public function articlesByAuthor(string $author): void
    { }

    /**
     * @param array $articles
     * @return void
     */
    public function saveArticles(array $articles = []): void
    {
        foreach ($articles as $article) {
            $author = null;
            if (!is_null($article['byline'])) {
                $author = $this->source->authors()->firstOrCreate([
                    'name' => str_replace("By ", '', $article['byline']),
                ]);
            }

            $imgUrl = null;
            if (array_key_exists('multimedia', $article) && count($article['multimedia']) > 0)
                $imgUrl = $article['multimedia'][0]['url'];

            $saved = $this->source->articles()->updateOrCreate(
                [
                    'author_id' => $author?->id,
                    'title' => $article['title'],
                ], [
                    'web_url' => $article['url'],
                    'image_url' => $imgUrl,
                    'description' => $article['abstract'],
                    'published_at' => is_null($article['published_date']) ? now() : Carbon::parse($article['published_date'])
                ]
            );

            $cat = $this->source->categories()->firstOrCreate(['name' => ucwords($article['section'])]);
            $saved->categories()->sync([$cat->id]);
        }

    }
}
