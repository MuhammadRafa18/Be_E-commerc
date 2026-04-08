<?php

namespace Database\Seeders;

use App\Models\SkinType;
use Illuminate\Database\Seeder;

class SkinTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $Skintypes = [
            [
                'type'     => 'Normal skin',
                'image'    => 'images/category/facewash.jpg',
            ],
            [
                'type'     => 'Dry skin',
                'image'    => 'images/category/moisturizer.jpg',
            ],
        ];

        foreach($Skintypes as $skin){
            SkinType::create($skin);
        }
    }
}
