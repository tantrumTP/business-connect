<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
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
            'name' => $this->name,
            'description' => $this->description,
            'price' => $this->price,
            'category' => $this->category,
            'availability' => $this->avaliability,
            'warranty' => $this->warranty,
            'path_alias' => $path_alias ? $path_alias->alias : $this->getOriginalPath(),
            'status' => $this->status
        ];
    }
}
