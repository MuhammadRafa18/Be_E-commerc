<?php

namespace App\Handlers\Product;

use App\Models\Product;
use App\Models\ProductSku;
use App\Models\ProductSkincare;
use Illuminate\Support\Facades\DB;


class SkincareProductHandler implements ProductHandlerInterface
{
    public function store($request, $category)
    {
        return DB::transaction(function () use ($request, $category) {

            // 1. Produk utama
            $product = Product::create([
                'title' => $request['title'],
                'category_id' => $category['id'],
                'description' => $request['description'],
                'image_produk' => $request['image_produk'],
                'image_banner' => $request['image_banner'],
            ]);

            // 2. SKU (1 saja)
            $sku = ProductSku::create([
                'product_id' => $product->id,
                'price' => $request['price'],
                'sell_price' => $request['sell_price'],
                'stock' => $request['stock'],
                'weight_gram' => $request['weight_gram'],
            ]);

            // 3. Detail skincare
            ProductSkincare::create([
                'product_sku_id' => $sku->id,
                'size' => $request['size'],
                'use_produk' => $request['use_produk'] ?? null,
                'ingredient' => $request['ingredient'] ?? null,
            ]);

            // 4. Skin type (opsional)
            if ($request['skin_type_id']) {
                $product->skin_type()->attach($request['skin_type_id']);
            }

            return $product;
        });
    }
    public function update($request, $product, $category)
    {
        return DB::transaction(function () use ($request, $product, $category) {


            $product->update([
                'title' => $request['title'] ?? $product->title,
                'category_id' => $request['category_id'] ?? $category->id,
                'description' => $request['description'] ?? $product->description,
                'image_produk' => $request['image_produk'] ?? $product->image_produk,
                'image_banner' => $request['image_banner'] ??  $product->image_banner,
                'is_active' => $request['is_active'] ??  $product->is_active,

            ]);

            $skuData = array_filter([
                'price'       => $request['price'] ?? null,
                'sell_price'  => $request['sell_price'] ?? null,
                'stock'       => $request['stock'] ?? null,
                'weight_gram' => $request['weight_gram'] ?? null,
            ], fn($v) => !is_null($v));  // buang yang null
        
            // Kalau ada data SKU yang dikirim, baru update
            if (!empty($skuData)) {
                $sku = ProductSku::updateOrCreate(
                    ['product_id' => $product->id],
                    $skuData  // hanya field yang dikirim yang terupdate
                );
            }

            $product_skincare = array_filter([
                'product_sku_id' => $sku->id ?? null,
                'size' => $request['size'] ?? null,
                'use_produk' => $request['use_produk'] ?? null,
                'ingredient' => $request['ingredient'] ?? null,
            ], fn($v) => !is_null($v));  // buang yang null

            // Kalau ada data SKU yang dikirim, baru update
            if (!empty($product_skincare)) {
                $product_skincare_detail = ProductSkincare::updateOrCreate(
                    ['product_sku_id' => $sku->id],
                    $product_skincare  // hanya field yang dikirim yang terupdate
                );
            }
        
            if (!empty($request['skin_type_id'])) {
                $product->skin_type()->sync($request['skin_type_id']);
            }

            return $product;
        });
    }
}
