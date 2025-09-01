<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\ProductDimension;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class ProductDimensionFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<\Illuminate\Database\Eloquent\Model>
     */
    protected $model = ProductDimension::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'width' => fake()->numberBetween(5, 500),
            'height' => fake()->numberBetween(5, 500),
            'depth' => fake()->numberBetween(5, 500),
        ];
    }
}
