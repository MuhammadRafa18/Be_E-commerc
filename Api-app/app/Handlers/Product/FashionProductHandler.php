<?php

namespace App\Handlers\Product;

use App\Models\Product;
use App\Models\ProductSku;
use App\Models\ProductFashion;
use Illuminate\Support\Facades\DB;

class FashionProductHandler implements ProductHandlerInterface
{
    public function store($request, $category)
    {
        return DB::transaction(function () use ($request, $category) {

            $product = Product::create([
                'title' => $request['title'],
                'category_id' => $category['id'],
                'description' => $request['description'],
                'image_produk' => $request['image_produk'],
                'image_banner' => $request['image_banner'],

            ]);

            $sku = ProductSku::create([
                'product_id' => $product->id,
                'price' => $request['price'],
                'sell_price' => $request['sell_price'],
                'stock' => $request['stock'],
                'weight_gram' => $request['weight_gram'],
            ]);

            foreach ($request['variants'] as $variant) {

                ProductFashion::create([
                    'product_sku_id' => $sku->id,
                    'size' => $variant['size'],
                    'color' => $variant['color'],
                ]);
            }

            return $product;
        });
    }
    public function update($request, $product, $category)
    {
        return DB::transaction(function () use ($request, $product, $category) {

            // \dd($request);
            $product->update([
                'title' => $request['title'] ?? $product->title,
                'category_id' => $request['category_id'] ?? $category->id,
                'description' => $request['description'] ?? $product->description,
                'image_produk' => $request['image_produk'] ?? $product->image_produk,
                'image_banner' => $request['image_banner'] ?? $product->image_banner,
                'is_active' => $request['is_active'] ??  $product->is_active,
            ]);

            $skuData = array_filter([
                'product_id' => $product->id,
                'price' => $request['price'] ?? null,
                'sell_price' => $request['sell_price'] ?? null,
                'stock' => $request['stock'] ?? null,
                'weight_gram' => $request['weight_gram'] ?? null,
            ], fn($v) => !is_null($v));


            if (!empty($skuData)) {
                $sku = ProductSku::updateOrCreate(
                    ['product_id' => $product->id],
                    $skuData
                );
            }

            if (!empty($request['variants'])) {
                foreach ($request['variants'] as $variant) {
                    $product_fashion = array_filter([
                         'id'             => $variant['id'] ?? null,
                        'size' => $variant['size'] ?? null,
                        'color' => $variant['color'] ?? null,
                    ], fn($v) => !is_null($v));
                    if (!empty($product_fashion)) {
                        $product_fashion_detail = ProductFashion::updateOrCreate(
                            [
                                'id'             => $variant['id'] ?? null, // ada id → update
                                'product_sku_id' => $sku->id,
                            ],
                            $product_fashion
                        );
                    }
                }
            }

            return $product;
        });
    }
}
