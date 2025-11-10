<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
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
       'addres' => $this->whenLoaded('addres'),
       'produk' => $this->whenLoaded('produk'),
       'qty' => $this->qty,
       'diskon' => $this->diskon,
       'ongkir' => $this->ongkir,
       'total' => $this->total,
       'status' => $this->status,
       'trackingNumber' => $this->trackingNumber,
        'created_at' => $this->created_at,

    ];


    }
}
