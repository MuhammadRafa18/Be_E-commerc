<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = fake()->unique()->words(3, true);
          return [
            'title' => fake()->words(2, true),
            'slug'         => Str::slug($title),
            'category_id' => Category::factory(),
            'description' => fake()->paragraph(),
            'image_produk' => 'product.jpg',
            'image_banner' => 'banner.jpg',
            'is_active' => true,
        ];
    }
}
