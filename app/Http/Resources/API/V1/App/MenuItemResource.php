<?php

namespace App\Http\Resources\API\V1\App;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MenuItemResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'           => $this->id,
            'name'         => $this->name,
            'description'  => $this->description,
            'base_price'   => (float) $this->base_price,
            'image_url'    => $this->image_urls,
            'category'     => $this->category?->name,
            'category_slug' => $this->category?->slug,
            'is_available' => (bool) $this->is_available,
            'sort_order'   => $this->sort_order,
        ];
    }
}
