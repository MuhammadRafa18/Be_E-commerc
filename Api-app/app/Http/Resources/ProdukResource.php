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
            'type_id' => $this->type_id,
            'category_id' => $this->category_id,
            'price' => $this->price,
            'size' => $this->size,
            'rating' => $this->rating,
            'stok' => $this->stok,
            'description' => $this->description,
            'useproduk' => $this->useproduk,
            'ingredient' => $this->ingredient,
             'created_at' => $this->created_at,

             
        ];
  
       
    }
}
