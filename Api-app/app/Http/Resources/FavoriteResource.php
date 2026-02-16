<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FavoriteResource extends JsonResource
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
            'user_id' => $this->user_id,
            'produk' => $this->whenLoaded('produk', fn () => new ProdukResource($this->produk)),
            'created_at' => $this->created_at
        ];
    }
}
