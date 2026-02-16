<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class About extends JsonResource
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
            'headline' => $this->headline,
            'title' => $this->title,
            'subtitle' => $this->subtitle,
            'slug' => $this->slug,
            'paragraf' => $this->paragraf,
            'image' => $this->image
                ? $this->image
                : null,
            'image_visi' => $this->image_visi
                ? $this->image_visi
                : null,
            'visi_misi' => $this->visi_misi,
            'icon' => $this->icon
                ? collect($this->icon)->map(fn($img) => $img)
                : [],
            'power' => $this->power ?? [],
            'created_at' => $this->created_at,
        ];
    }
}
