<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SkinTypesResource extends JsonResource
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
            'type' => $this->type,
            'slug' => $this->slug,
            'image' => $this->image,
             'created_at' => $this->created_at,
        ];
    }
}
