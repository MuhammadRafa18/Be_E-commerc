<?php

namespace App\Http\Resources;


use Illuminate\Http\Resources\Json\JsonResource;

class Cart extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,

            // product utama
            'product' => $this->whenLoaded('product', function () {
                return [
                    'id' => $this->product->id,
                    'title' => $this->product->title,
                ];
            }),

            // sku (harga, stock, dll)
            'product_sku' => $this->whenLoaded('product_sku', function () {
                return [
                    'id' => $this->product_sku->id,
                    'price' => $this->product_sku->price,
                    'sell_price' => $this->product_sku->sell_price,
                    'stock' => $this->product_sku->stock,
                ];
            }),

            // 🔥 ini yang penting (variant terpilih)
            'variant' => $this->resolveVariant(),

            'qty' => $this->qty,
            'is_selected' => $this->is_selected,
        ];
    }

    private function resolveVariant()
    {
        // fashion
        if ($this->product_fashion_id && $this->relationLoaded('product_fashion') && $this->product_fashion) {
            return [
                'type' => 'fashion',
                'id' => $this->product_fashion->id,
                'size' => $this->product_fashion->size,
                'color' => $this->product_fashion->color,
            ];
        }

        // skincare
        if ($this->product_skincare_id && $this->relationLoaded('product_skincare') && $this->product_skincare) {
            return [
                'type' => 'skincare',
                'id' => $this->product_skincare->id,
                'size' => $this->product_skincare->size,
                'use_produk' => $this->product_skincare->use_produk,
                'ingredient' => $this->product_skincare->ingredient,
            ];
        }

        return null;
    }
}
