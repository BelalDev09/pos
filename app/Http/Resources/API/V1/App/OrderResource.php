<?php

namespace App\Http\Resources\API\V1\App;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
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
            'table'        => [
                'id'     => $this->table?->id,
                'number' => str_pad($this->table?->table_number ?? 0, 2, '0', STR_PAD_LEFT),
            ],
            'type'         => $this->order_type,   // waiter | qr
            'status'       => $this->status, // pending|preparing|ready|served|cancelled|closed
            'item_count'   => $this->items?->count() ?? 0,
            'total'        => (float) ($this->grand_total ?? $this->sub_total ?? 0),
            'order_note'   => $this->order_note ?? '',
            'created_at'   => $this->created_at?->toIso8601String(),
            'time_ago'     => $this->created_at?->diffForHumans(null, true),
        ];
    }
}
