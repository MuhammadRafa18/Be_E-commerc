<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FavoriteProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'image_produk' => $this->image_produk,
            'title' => $this->title,
            'category' => $this->whenLoaded('category', function () {
                return [
                    'id' => $this->category->id,
                    'type' => $this->category->type,
                ];
            }),
            'product_sku' => $this->whenLoaded('product_sku', function () {

                return [
                    'price' => $this->product_sku->min('price'),
                    'sell_price' => $this->product_sku->min('sell_price'),
                    'stock' => $this->product_sku->sum('stock'),
                ];
            }),
        ];
    }
}
