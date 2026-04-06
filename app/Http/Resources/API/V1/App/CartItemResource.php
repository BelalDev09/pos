<?php

namespace App\Http\Resources\API\V1\App;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CartItemResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'cart_item_id' => $this->id,
            'menu_item_id' => $this->menu_item_id,
            'name' => $this->menuItem?->name,
            'category' => $this->menuItem?->category?->name,
            'image_urls' => $this->menuItem?->images
                ? collect($this->menuItem->images)->map(fn($img) => asset($img))->all()
                : [],
            'size' => $this->size?->name ?? $this->menuItem?->size ?? null,
            'unit_price' => (float) $this->unit_price,
            'quantity' => (int) $this->quantity,
            'subtotal' => round($this->unit_price * $this->quantity, 2),
            'note' => $this->note ?? '',
            'is_held' => (bool) $this->is_held,
            'add_ons' => $this->addOns?->map(fn($ingredient) => [
                'id' => $ingredient->id,
                'name' => $ingredient->name,
                'extra_price' => (float) ($ingredient->pivot->extra_price ?? 0)
            ])->values() ?? [],
        ];
    }
}
