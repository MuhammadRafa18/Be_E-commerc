<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ParagrafAboutResource extends JsonResource
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
            'imageabout' => $this->imageabout,
            'paragrafabout1' => $this->paragrafabout1,
            'paragrafabout2' => $this->paragrafabout2,
            'paragrafabout3' => $this->paragrafabout3,
            'paragrafabout4' => $this->paragrafabout4,
             'created_at' => $this->created_at,
        ];
    }
}
