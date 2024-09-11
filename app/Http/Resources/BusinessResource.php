<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BusinessResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'direction' => $this->direction,
            'phone' => $this->phone,
            'email' => $this->email,
            'hours' => $this->hours,
            'website' => $this->website,
            'social_networks' => $this->social_networks,
            'characteristics' => $this->characteristics,
            'covered_areas' => $this->covered_areas,
        ];
    }
}
