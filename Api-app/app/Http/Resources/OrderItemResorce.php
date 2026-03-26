<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderItemResorce extends JsonResource
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
            'order' => $this->whenLoaded('order') ?? [],
            'produk' => $this->whenLoaded('produk') ?? [],
            'produk_title' => $this->product_title,
            'produk_price' => $this->product_price,
            'subtotal' => $this->subtotal,
            'qty' => $this->qty,
            'created_at' => $this->created_at,
        ];
    }
}
