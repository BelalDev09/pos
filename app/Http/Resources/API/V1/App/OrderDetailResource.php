<?php

namespace App\Http\Resources\API\V1\App;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderDetailResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'         => $this->id,
            'table'      => [
                'id'     => $this->table?->id,
                'number' => str_pad($this->table?->table_number ?? 0, 2, '0', STR_PAD_LEFT),
            ],
            'type'       => $this->order_type,
            'status'     => $this->status,
            'order_note' => $this->order_note ?? '',
            'total' => (float) $this->grand_total,
            'created_at' => $this->created_at?->toIso8601String(),
            'items'      => ($this->items ?? collect())->map(fn($i) => [
                'id'           => $i->id,
                'menu_item_id' => $i->menu_item_id,
                'name'         => $i->menuItem?->name,
                'category'     => $i->menuItem?->category?->name,
                'image_url'    => $i->menuItem?->image_url,
                'size' => $i->variant?->name ?? $i->menuItem?->size ?? '',
                'unit_price'   => (float) $i->unit_price,
                'quantity'     => $i->quantity,
                'subtotal'     => round($i->unit_price * $i->quantity, 2),
                'note' => $i->notes ?? '',
                'is_held'      => (bool) $i->is_held,
                'status'       => $i->order->status, // preparing|ready|served
            ])->values(),
        ];
    }
}
