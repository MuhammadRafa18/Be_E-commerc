<?php

namespace Database\Seeders;

use App\Models\Addres;
use Illuminate\Database\Seeder;

class AddresSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Addres::create([
            'user_id' => 2,
            'fullname' => 'Rafa',
            'streetname' => 'Jl palang Kara',
            'place' => 'Home',
            'provinci' => 'Jawa Barat',
            'city' => 'Depok',
        ]);
    }
}
