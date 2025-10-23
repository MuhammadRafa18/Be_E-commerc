<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VisiMisiResource extends JsonResource
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
            'visimisi1' => $this->visimisi1,
            'visimisi2' => $this->visimisi2,
            'visimisi3' => $this->visimisi3,
            'visimisi4' => $this->visimisi4,
             'created_at' => $this->created_at,
        ];
    }
}
