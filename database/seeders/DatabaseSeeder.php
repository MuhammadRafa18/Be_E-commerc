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
        \App\Models\User::factory(10)->create();
        \App\Models\Addres::factory(10)->create();
        \App\Models\Category::factory(10)->create();
        \App\Models\SkinType::factory(10)->create();
        \App\Models\Product::factory(20)->create();
        \App\Models\ProductSku::factory(20)->create();
        \App\Models\ProductFashion::factory(10)->create();
        \App\Models\ProductSkincare::factory(10)->create();
        \App\Models\ShippingZone::factory(10)->create();
        \App\Models\ZoneRegion::factory(10)->create();
    }
    //     $seeders = [
          
    //         UserSeeder::class,      
    //         AddresSeeder::class,   
    //         CategorySeeder::class,   
    //         SkinTypeSeeder::class,
    //         ProductSeeder::class,  
    //         ShippingZoneSeeder::class,
    //         ZoneSeeder::class,
    //     ];

    //     $this->call($seeders);
    // }
}
