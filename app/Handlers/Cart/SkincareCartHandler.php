<?php

namespace App\Handlers\Cart;

use App\Models\ProductSkincare;
use Illuminate\Http\Exceptions\HttpResponseException;

class SkincareCartHandler implements CartHandlerInterface
{
    public function resolveVariant(array $data, $product): array
    {
            $skincare = ProductSkincare::where('id', $data['product_skincare_id'] ?? null)
                ->whereHas('product_sku', function ($q) use ($product) {
                    $q->where('product_id', $product->id);
                })
                ->first();

        if (!$skincare) {
            throw new HttpResponseException(
                response()->json([
                    'success' => false,
                    'message' => 'Variant skincare tidak valid',
                ], 422)
            );
        }

        return [
            'sku_id' => $skincare->product_sku_id,
            'fashion_id' => null,
            'skincare_id' => $skincare->id,
        ];
    }
}
