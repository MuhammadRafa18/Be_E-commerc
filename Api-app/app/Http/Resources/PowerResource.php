<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PowerResource extends JsonResource
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
            'icon' => $this->icon,
            'power' => $this->power,
             'created_at' => $this->created_at,
        ];
    }
}
