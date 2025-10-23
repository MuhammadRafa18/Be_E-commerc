<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DataUserResource extends JsonResource
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
           'email' => $this->email,
           'fullname' => $this->fullname,
           'password' => $this->password,
           'phone' => $this->phone,
           'created_at' => $this->created_at
        ];
    }
}
