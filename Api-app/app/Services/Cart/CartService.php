<?php

namespace App\Services\Cart;

use App\Handlers\Cart\FashionCartHandler;
use App\Handlers\Cart\SkincareCartHandler;
use App\Models\Cart;
use App\Models\Product;

class CartService
{
    public function addToCart(array $data, $user)
    {
        $product = Product::with('category')->findOrFail($data['product_id']);
        $handler = $this->resolveHandler($product->category->type);
        
        $variant = $handler->resolveVariant($data, $product);
        // \dd($variant);

        return Cart::firstOrCreate(
            [
                'user_id' => $user->id,
                'product_sku_id' => $variant['sku_id'],
                'product_fashion_id' => $variant['fashion_id'] ?? \null,
                'product_skincare_id' => $variant['skincare_id'] ?? \null,
            ],
            [
                'product_id' => $product->id,
                'qty' => $data['qty'] ?? 1,
            ]
        );
    }

    private function resolveHandler($type)
    {
        return match ($type) {
            'fashion' => new FashionCartHandler(),
            'skincare' => new SkincareCartHandler(),
            default => throw new \Exception('Type tidak didukung', 422),
        };
    }
}
