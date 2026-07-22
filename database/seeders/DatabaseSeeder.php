<?php

namespace Database\Seeders;

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

        // Eksekusi semua seeder sesuai urutan array di atas
        $this->call($seeders);
    }
}
