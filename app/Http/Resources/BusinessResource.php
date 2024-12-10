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
        $path_alias = $this->getPathAlias();
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
            'media' => $this->when($this->additional['media'] ?? null, function () {
                return [
                    'data' => MediaResource::collection($this->additional['media']->items()),
                    'pagination' => [
                        'total' => $this->additional['media']->total(),
                        'per_page' => $this->additional['media']->perPage(),
                        'current_page' => $this->additional['media']->currentPage(),
                        'last_page' => $this->additional['media']->lastPage(),
                    ],
                ];
            }),
            'path_alias' => $path_alias ? $path_alias->alias : $this->getOriginalPath()
        ];
    }
}
