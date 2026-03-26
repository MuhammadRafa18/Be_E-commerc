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
            'address_id' => $this->address_id,
            'zones_region_id' => $this->zones_region_id,
            'invoice_number' => $this->invoice_number,
            'shipping_name' => $this->shipping_name,
            'shipping_phone' => $this->shipping_phone,
            'shipping_street' => $this->shipping_street,
            'shipping_city' => $this->shipping_city,
            'shipping_province' => $this->shipping_province,
            'diskon' => $this->diskon,
            'ongkir' => $this->ongkir,
            'subtotal' => $this->subtotal,
            'total' => $this->total,
            'status' => $this->status,
            'trackingNumber' => $this->trackingNumber,
            'created_at' => $this->created_at,



        ];
    }
}
