<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;


class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'category' => 'Facewash',
                'type'     => 'skincare',
            ],
            [
                'category' => 'Moisturizer',
                'type'     => 'skincare',
            ],
            [
                'category' => 'Kaos',
                'type'     => 'fashion',
            ],
            [
                'category' => 'Celana',
                'type'     => 'fashion',
            ],
        ];
        foreach ($categories as $cat) {
            Category::create($cat); // slug otomatis dari boot()
        }
    }
}
