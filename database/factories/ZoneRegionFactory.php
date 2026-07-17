<?php

namespace Database\Factories;

use App\Models\ShippingZone;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ZoneRegion>
 */
class ZoneRegionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'shipping_zone_id' => ShippingZone::factory(),
            'region'           => fake()->city(),
            'estimasi_min_day' => fake()->numberBetween(1, 3),
            'estimasi_max_day' => fake()->numberBetween(4, 7),
        ];
    }
}
