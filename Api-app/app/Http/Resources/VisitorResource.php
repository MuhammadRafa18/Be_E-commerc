<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VisitorResource extends JsonResource
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
            'ip_address' => $this->ip_address,
            'visitor' => $this->visitor,
            'created_at' => $this->created_at,
        ];
    }
}
