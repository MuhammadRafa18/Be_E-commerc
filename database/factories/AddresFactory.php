<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Addres>
 */
class AddresFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
         return [
            'user_id'    => User::factory(),
            'fullname'   => fake()->name(),
            'streetname' => fake()->streetAddress(),
            'place'      => fake()->randomElement([
                'Rumah',
                'Kantor'
            ]),
            'provinci'   => fake()->state(),
            'city'       => fake()->city(),
        ];
    }
}
