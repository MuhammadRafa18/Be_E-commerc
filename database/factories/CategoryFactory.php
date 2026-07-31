<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

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
        $type = fake()->randomElement(['fashion', 'skincare']);
        $title = fake()->unique()->words(3, true);
        return [
            'category' => fake()->unique()->word(),
            'slug'         => Str::slug($title),
            'type' => $type,
        ];
    }
    public function fashion(): static
    {
        return $this->state(fn() => [
            'type' => 'fashion',
        ]);
    }

    public function skincare(): static
    {
        return $this->state(fn() => [
            'type' => 'skincare',
        ]);
    }
}
