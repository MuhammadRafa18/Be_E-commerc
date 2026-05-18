<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ZoneRegion extends JsonResource
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
            'shipping_zone' => $this->whenLoaded('shipping_zone'),
            'region' => $this->region,
            'estimasi_min_day' => $this->estimasi_min_day,
            'estimasi_max_day' => $this->estimasi_max_day,
        ];
    }
}
