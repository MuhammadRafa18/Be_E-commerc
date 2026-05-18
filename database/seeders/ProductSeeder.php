<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\ProductFashion;
use App\Models\ProductSkincare;
use App\Models\ProductSku;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
{
    // ==================
    // PRODUCT FASHION
    // ==================
    $fashion = Product::create([
        'title'        => 'Levis',
        'category_id'  => 4, // fashion
        'description'  => 'lorem',
        'image_produk' => 'image_produks/dummy-fashion.jpg',
        'image_banner' => 'image_banner/dummy-fashion-banner.jpg',
    ]);

    $fashionSku = ProductSku::create([
        'product_id'  => $fashion->id,
        'price'       => 250000,
        'sell_price'  => 200000,
        'stock'       => 12,
        'weight_gram' => 150,
    ]);

    $fashionVariants = [
        ['size' => 'S',  'color' => 'Blue'],
        ['size' => 'M',  'color' => 'Blue'],
        ['size' => 'L',  'color' => 'Black'],
        ['size' => 'XL', 'color' => 'Black'],
    ];

    foreach ($fashionVariants as $variant) {
        ProductFashion::create([
            'product_sku_id' => $fashionSku->id,
            'size'           => $variant['size'],
            'color'          => $variant['color'],
        ]);
    }

    // ==================
    // PRODUCT SKINCARE
    // ==================
    $skincare = Product::create([
        'title'        => 'Facewash Oild',
        'category_id'  => 2, // skincare
        'description'  => 'lorem',
        'image_produk' => 'image_produks/dummy-skincare.jpg',
        'image_banner' => 'image_banner/dummy-skincare-banner.jpg',
    ]);

    $skincareSku = ProductSku::create([
        'product_id'  => $skincare->id,
        'price'       => 200000,
        'sell_price'  => 150000,
        'stock'       => 20,
        'weight_gram' => 200,
    ]);

    ProductSkincare::create([
        'product_sku_id' => $skincareSku->id,
        'size'           => '65 ml',
        'use_produk'     => 'lorem',
        'ingredient'     => 'ingredient',
    ]);

    // Attach skin type
    $skincare->skin_type()->attach([1]);
}
}
