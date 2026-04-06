<?php

namespace App\Http\Resources\API\V1\App;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CartResource extends JsonResource
{
    public function toArray(Request $request): array
    {

        $items = $this->items ?? collect();

        $total = $items->sum(function ($item) {
            return $item->unit_price * $item->quantity;
        });
        // $itemOrderNote = $item->orderItem?->notes ?? '';
        return [

            'table_id' => $this->restaurant_tables_id,

            'item_count' => $items->sum('quantity'),

            // 'order_note' => $this->order_note ?? '',

            'total' => round($total, 2),

            'items' => $items->map(function ($item) {
                return [
                    'cart_item_id' => $item->id,

                    'menu_item_id' => $item->menu_item_id,

                    'name' => $item->menuItem?->name,

                    'category' => $item->menuItem?->category?->name,

                    'image_urls' => $item->menuItem?->images
                        ? collect($item->menuItem->images)->map(fn($img) => asset($img))->all()
                        : [],

                    'size' => $item->size?->name ?? $item->menuItem?->size ?? null,
                    // 'size' => $this->size?->name ?? $this->menuItem?->size ?? null,
                    'unit_price' => (float) $item->unit_price,

                    'quantity' => (int) $item->quantity,

                    'subtotal' => round(
                        $item->unit_price * $item->quantity,
                        2
                    ),

                    'note' => $item->note ?? '',

                    'is_held' => (bool) $item->is_held,

                    'add_ons' => $item->addOns?->map(function ($ingredient) {
                        return [
                            'id' => $ingredient->id,
                            'name' => $ingredient->name,

                            'extra_price' => (float) (
                                $ingredient->pivot->extra_price ?? 0
                            ),
                        ];
                    })->values() ?? [],
                ];
            })->values(),
        ];
    }
}
