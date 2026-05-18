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
            'product' => $this->whenLoaded('product', fn () => new ProductResource($this->product)),
            'created_at' => $this->created_at
        ];
    }
}
