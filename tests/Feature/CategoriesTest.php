<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CategoriesTest extends TestCase
{

    public function test_example(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }
}