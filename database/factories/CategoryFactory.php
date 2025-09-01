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
        $category = fake()->randomElement(['Smartphone', 'Laptop', 'Toy', 'Headphone',  'Furniture', 'Gaming']);
        return [
            'name' => $category,
            'slug' => str($category)->slug(),
            'url' =>  'http://localhost/api/products/' . str($category)->slug(),

        ];
    }
}
