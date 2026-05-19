<?php

namespace App\Services\Cart;

use App\Handlers\Cart\FashionCartHandler;
use App\Handlers\Cart\SkincareCartHandler;
use App\Models\Cart;
use App\Models\ProductSku;

class CartService
{
    public function addToCart(array $data, $user)
    {
        $sku = ProductSku::with('product.category')->findOrFail($data['sku_id'] ?? $data['product_sku_id']);
        $product = $sku->product;
        $handler = $this->resolveHandler($product->category->type);
        $variant = $handler->resolveVariant($data, $product);

        $qtyRequest = $data['qty'] ?? 1;
        $cart = Cart::firstOrCreate(
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
        $totalQtyBaru = $cart->qty + $qtyRequest;
        try {
            if ($totalQtyBaru > $sku->stock) {
                throw new \Exception("Gagal: Total di keranjangmu ({$totalQtyBaru} pcs) melebihi stok gudang ({$sku->stock} pcs).");
            }
            $cart->qty = $totalQtyBaru;
            $cart->save();
        } catch (\Exception $e) {
            $cart->qty = 0;
            $cart->save();
            throw new \Exception($e->getMessage());
        }

        return $cart;
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
