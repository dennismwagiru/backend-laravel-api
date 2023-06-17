<?php

namespace App\Domain\News;

use App\Models\Source;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;

class TheGuardianApiService implements ApiService
{

    const BASE_URL = 'https://content.guardianapis.com/';
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
        $response = Http::withHeaders($this->getHeaders())
            ->get(self::BASE_URL . 'search', 'api-key=' . $this->source->api_key);

        if ($response->successful() && strtolower($response->json('response.status')) == 'ok')
            $this->saveArticles($response->json('response.results'));

    }

    /**
     * @return string[]
     */
    public function categories(): array
    {
        return [];
    }

    /**
     * @param string $category
     * @return void
     */
    public function articlesByCategory(string $category): void
    { }

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
            $saved = $this->source->articles()->updateOrCreate(
                [
                    'title' => $article['webTitle'],
                    'web_url' => $article['webUrl'],
                ], [
                    'published_at' => is_null($article['webPublicationDate']) ? now() : Carbon::parse($article['webPublicationDate'])
                ]
            );

            $cat = $this->source->categories()->firstOrCreate(['name' => ucwords($article['sectionName'])]);
            $saved->categories()->sync([$cat->id]);
        }

    }
}
