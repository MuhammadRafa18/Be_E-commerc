<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DetailFaqResource extends JsonResource
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
            'faq' => $this->whenLoaded('faq'),
            'quest' => $this->quest,
            'answer' => $this->answer,
             'created_at' => $this->created_at,
        ];
    }
}
