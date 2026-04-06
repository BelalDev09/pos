<?php

namespace App\Http\Resources\API\V1\App;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MenuItemDetailResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'          => $this->id,
            'name'        => $this->name,
            'description' => $this->description,
            'base_price'  => (float) $this->base_price,
            'image_url'   => $this->image_url,
            'category'    => $this->category?->name,
            'is_available' => (bool) $this->is_available,

            //variants size
            'variants_sizes' => $this->sizes->map(fn($s) => [
                'id'         => $s->id,
                'size'      => $s->name,
                'price'      => (float) $s->price_adjustment . ' TK',
                'is_default' => (bool) $s->is_default,
            ]),

            //  Add ingredients
            'ingredients_add_ons' => $this->addOns->map(fn($a) => [
                'id'       => $a->id,
                'name'     => $a->name,
                'quantity' =>  $a->pivot->quantity ?? 1,
                'price'    => (float) $a->pivot->extra_price . ' TK',
            ]),
        ];
    }
}
