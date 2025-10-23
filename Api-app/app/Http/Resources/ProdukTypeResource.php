<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProdukTypeResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // return parent::toArray($request);
         return  [
            'id' => $this->id,
            'image' => $this->image,
            'type_id' => $this->type_id,
             'created_at' => $this->created_at,
        ];
    }
}
