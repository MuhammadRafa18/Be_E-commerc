<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AddresResource extends JsonResource
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
        'id'=> $this->id,    
        'user_id' => $this->user_id,
        'fullname' => $this->fullname,
        'streetname' => $this->streetname,
        'place' => $this->place,
        'provinci' => $this->provinci,
        'city' => $this->city,
        'created_at' => $this->created_at,
    ];
    }
}
