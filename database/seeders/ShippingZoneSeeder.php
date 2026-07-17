<?php

namespace Database\Seeders;

use App\Models\ShippingZone;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ShippingZoneSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
          ShippingZone::create([
            'id' => 3,
            'name' => 'zone 1',
            'price' => 100000,       
        ]);
    }
}
