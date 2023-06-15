<?php

namespace Tests\Feature;

use App\Models\Author;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AuthorTest extends TestCase
{
    use WithFaker;

    const ROUTE_LIST_AUTHORS = 'authors.index';
    const ROUTE_SHOW_AUTHORS = 'authors.show';

    public function test_authors_list(): void
    {
        Author::factory(20)->create();

        $response = $this->get(route(self::ROUTE_LIST_AUTHORS));

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [['id', 'name', 'source', 'description', 'created_at']],
                'meta' => [
                    'pagination' => [
                        'total', 'count', 'per_page', 'current_page', 'total_pages', 'links'
                    ]
                ]
            ]);
    }

    public function test_authors_list_empty(): void
    {

        $response = $this->get(route(self::ROUTE_LIST_AUTHORS));

        $response->assertStatus(200)
            ->assertJsonCount(0, 'data')
            ->assertJsonPath('meta.pagination.total', 0)
            ->assertJsonStructure([
                'data',
                'meta' => [
                    'pagination' => [
                        'total', 'count', 'per_page', 'current_page', 'total_pages', 'links'
                    ]
                ]
            ]);
    }

    public function test_authors_list_pagination(): void
    {
        Author::factory(20)->create();

        $response = $this->get(route(self::ROUTE_LIST_AUTHORS) . '?page=2&per_page=10');

        $response->assertStatus(200)
            ->assertJsonCount(10, 'data')
            ->assertJsonPath('meta.pagination.total', 20)
            ->assertJsonPath('meta.pagination.count', 10)
            ->assertJsonPath('meta.pagination.per_page', 10)
            ->assertJsonPath('meta.pagination.current_page', 2)
            ->assertJsonPath('meta.pagination.total_pages', 2)
            ->assertJsonStructure([
                'data',
                'meta' => [
                    'pagination' => [
                        'total', 'count', 'per_page', 'current_page', 'total_pages', 'links'
                    ]
                ]
            ]);
    }

    public function test_authors_list_search_mismatching_name(): void
    {
        Author::factory()->create();

        $query = $this->faker->unique()->text(10);

        $response = $this->get(route(self::ROUTE_LIST_AUTHORS) . '?query='.$query);

        $response->assertStatus(200)
            ->assertJsonCount(0, 'data')
            ->assertJsonPath('meta.pagination.total', 0)
            ->assertJsonStructure([
                'data',
                'meta' => [
                    'pagination' => [
                        'total', 'count', 'per_page', 'current_page', 'total_pages', 'links'
                    ]
                ]
            ]);
    }

    public function test_authors_list_search_matching_name(): void
    {
        $author = Author::factory()->create();

        $response = $this->get(route(self::ROUTE_LIST_AUTHORS) . '?query='.$author->name);

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('meta.pagination.total', 1)
            ->assertJsonStructure([
                'data',
                'meta' => [
                    'pagination' => [
                        'total', 'count', 'per_page', 'current_page', 'total_pages', 'links'
                    ]
                ]
            ]);
    }

    public function test_authors_list_filter_by_source(): void
    {
        $author = Author::factory()->create();

        $response = $this->get(route(self::ROUTE_LIST_AUTHORS) . '?source='.$author->source);

        $response->assertStatus(200)
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('meta.pagination.total', 1)
            ->assertJsonStructure([
                'data',
                'meta' => [
                    'pagination' => [
                        'total', 'count', 'per_page', 'current_page', 'total_pages', 'links'
                    ]
                ]
            ]);
    }

    public function test_authors_show(): void
    {
        $author = Author::factory()->create();

        $response = $this->get(route(self::ROUTE_SHOW_AUTHORS, ['author' => $author->id]));

        $response->assertStatus(200)
            ->assertJsonPath('id', $author->id)
            ->assertJsonPath('name', $author->name)
            ->assertJsonPath('description', $author->description);
    }

    public function test_authors_show_none(): void
    {
        $response = $this->get(route(self::ROUTE_SHOW_AUTHORS, ['author' => $this->faker->numberBetween()]));

        $response->assertStatus(404);
    }

}
