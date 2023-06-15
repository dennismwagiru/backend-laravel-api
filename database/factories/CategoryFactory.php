<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Category>
 */
class CategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'sources' => $this->faker->randomElements(array_keys(config('settings.sources')), 2, false),
            'name' => $this->faker->text(30),
            'description' => $this->faker->text
        ];
    }
}
