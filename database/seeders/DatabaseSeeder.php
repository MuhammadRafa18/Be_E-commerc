<?php

namespace Database\Seeders;

use App\Models\Addres;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductFashion;
use App\Models\ProductSkincare;
use App\Models\ProductSku;
use App\Models\ShippingZone;
use App\Models\SkinType;
use App\Models\ZoneRegion;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $seeders = [
          
            UserSeeder::class,      
            AddresSeeder::class,   
            CategorySeeder::class,   
            ProductSeeder::class,  
            SkinTypeSeeder::class,
            ShippingZoneSeeder::class,
            ZoneSeeder::class,
        ];

        $this->call($seeders);
    }
}
