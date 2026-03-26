<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProdukResource extends JsonResource
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
            'imageproduk' => $this->imageproduk,
            'imagebanner' => $this->imagebanner,
            'title' => $this->title,
            'slug' => $this->slug,
            'category' => $this->whenLoaded('category'),
            'skin_type' => $this->skin_type->pluck('id'),
            'price' => $this->price,
            'sell_price' => $this->sell_price,
            'size' => $this->size,
            'stok' => $this->stok,
            'description' => $this->description,
            'useproduk' => $this->useproduk,
            'ingredient' => $this->ingredient,
            'is_active' => $this->is_active,
            'created_at' => $this->created_at,


        ];
    }
}
