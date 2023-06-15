<?php

namespace Tests\Feature;

use App\Models\Category;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CategoriesTest extends TestCase
{
    use WithFaker;

    const ROUTE_STORE_CATEGORIES = 'categories.store';
    const ROUTE_UPDATE_CATEGORIES = 'categories.update';
    const ROUTE_LIST_CATEGORIES = 'categories.index';
    const ROUTE_SHOW_CATEGORY = 'categories.show';

    public function test_categories_list(): void
    {
        Category::factory(20)->create();

        $response = $this->get(route(self::ROUTE_LIST_CATEGORIES));

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [['id', 'name', 'sources', 'description', 'created_at']],
                'meta' => [
                    'pagination' => [
                        'total', 'count', 'per_page', 'current_page', 'total_pages', 'links'
                    ]
                ]
            ]);
    }

    public function test_categories_list_empty(): void
    {

        $response = $this->get(route(self::ROUTE_LIST_CATEGORIES));

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

    public function test_categories_list_pagination(): void
    {
        Category::factory(20)->create();

        $response = $this->get(route(self::ROUTE_LIST_CATEGORIES) . '?page=2&per_page=10');

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

    public function test_categories_list_search_mismatching_name(): void
    {
        Category::factory()->create();

        $query = $this->faker->unique()->text(10);

        $response = $this->get(route(self::ROUTE_LIST_CATEGORIES) . '?query='.$query);

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

    public function test_categories_list_search_matching_name(): void
    {
        $category = Category::factory()->create();

        $response = $this->get(route(self::ROUTE_LIST_CATEGORIES) . '?query='.$category->name);

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

    public function test_categories_show(): void
    {
        $category = Category::factory()->create();

        $response = $this->get(route(self::ROUTE_SHOW_CATEGORY, ['category' => $category->id]));

        $response->assertStatus(200)
            ->assertJsonPath('id', $category->id)
            ->assertJsonPath('name', $category->name)
            ->assertJsonPath('description', $category->description);
    }

    public function test_categories_show_none(): void
    {
        $response = $this->get(route(self::ROUTE_SHOW_CATEGORY, ['category' => $this->faker->numberBetween()]));

        $response->assertStatus(404);
    }
}
