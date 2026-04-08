<?php

namespace App\Handlers\Cart;

use App\Models\ProductFashion;
use Illuminate\Http\Exceptions\HttpResponseException;

class FashionCartHandler implements CartHandlerInterface
{
    public function resolveVariant(array $data, $product): array
    {
        $fashion = ProductFashion::where('id', $data['product_fashion_id'] ?? null)
            ->whereHas('product_sku', function ($q) use ($product) {
                $q->where('product_id', $product->id);
            })
            ->first();

        if (!$fashion) {
            throw new HttpResponseException(
                response()->json([
                    'success' => false,
                    'message' => 'Variant fashion tidak valid',
                ], 422)
            );
        }

        return [
            'sku_id' => $fashion->product_sku_id,
            'fashion_id' => $fashion->id,
            'skincare_id' => null,
        ];
    }
}
