<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // return parent::toArray($request);
        return [
            'id' => $this->id,
            'image_produk' => $this->image_produk,
            'image_banner' => $this->image_banner,
            'title' => $this->title,
            'slug' => $this->slug,
            'category' => $this->whenLoaded('category'),
            'skin_type' => $this->whenLoaded('skin_type', function () {
                return $this->skin_type->map(function ($item) {
                    return [
                        'id' => $item->id,
                        'type' => $item->type,
                    ];
                });
            }),
            'description' => $this->description,
            'product_sku' => $this->when($this->product_sku->isNotEmpty(), function () {
                $sku = $this->product_sku->first(); // ambil satu SKU saja

                return [
                    'price'      => $sku->price,
                    'sell_price' => $sku->sell_price,
                    'stock'      => $sku->stock,
                    'weight_gram' => $sku->weight_gram,
                    'detail'     => $this->resolveDetail($sku), // detail di-loop di dalam
                ];
            }),

            'is_active' => $this->is_active,

        ];
    }

    // private function resolveDetail($sku)
    // {
    //     return match ($this->category->type) {
    //         'fashion' => [
    //             'size' => $sku->attribute->size ?? null,
    //             'color' => $sku->attribute->color ?? null,
    //         ],

    //         'skincare' => [
    //             'size' => $sku->skincare->size ?? null,
    //             'use_produk' => $sku->skincare->use_produk ?? null,
    //             'ingredient' => $sku->skincare->ingredient ?? null,
    //         ],

    //         default => null
    //     };
    // }
    private function resolveDetail($sku)
    {
        return match ($this->category->type) {
            'fashion' => $sku->attribute->map(fn($attr) => [
                'id' => $attr->id ?? null,
                'size'  => $attr->size ?? null,
                'color' => $attr->color ?? null,
            ]),

            'skincare' => [
                'size'       => $sku->skincare->size ?? null,
                'use_produk' => $sku->skincare->use_produk ?? null,
                'ingredient' => $sku->skincare->ingredient ?? null,
            ],

            default => null
        };
    }
}
