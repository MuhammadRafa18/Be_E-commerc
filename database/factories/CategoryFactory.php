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
        $type = fake()->randomElement(['fashion', 'skincare']);
        return [
            'category' => fake()->unique()->word(),
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
