<?php

namespace Database\Seeders;

use App\Models\ZoneRegion;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ZoneSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
          ZoneRegion::create([
            'id' => 3,
            'shipping_zone_id' => 3,
            'region' => 'jawa barat',       
            'estimasi_min_day' => 1,       
            'estimasi_max_day' => 4,       
        ]);
    }
}
