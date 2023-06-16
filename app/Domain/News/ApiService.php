<?php

namespace App\Domain\News;

use App\Models\Article;

interface ApiService
{

    public function getHeaders(): array;

    /**
     * Fetch the latest articles
     *
     * @return void
     */
    public function articles(): void;

    /**
     * Get categories
     *
     * @return array<string>
     */
    public function categories(): array;

    /**
     * Fetch articles by category
     *
     * @param string $category
     * @return void
     */
    public function articlesByCategory(string $category): void;

    /**
     * Fetch articles by author
     *
     * @param string $author
     * @return void
     */
    public function articlesByAuthor(string $author): void;

    /**
     * Save stories to db
     *
     * @param array $articles
     * @return void
     */
    public function saveArticles(array $articles): void;

}