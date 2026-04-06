<?php

namespace App\Http\Resources\API\V1\App;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RestaurantTableResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $occupiedSeats = $this->seats->where('is_occupied', true)->count();
        $reservedSeats = $this->seats->where('is_reserved', true)->count();

        return [
            'id'              => $this->id,
            'table_number'    => str_pad($this->table_number, 2, '0', STR_PAD_LEFT),
            'capacity'        => $this->capacity,
            'occupied_seats'  => $occupiedSeats,
            'reserved_seats'  => $reservedSeats,
            'available_seats' => $this->capacity - $occupiedSeats - $reservedSeats,
            'status'          => $this->status,
            'payment_status'  => $this->payment_status ?? 'unpaid',
            'active_order_id' => $this->activeOrder?->id,
            'seats'           => RestaurantSeatResource::collection($this->seats),
        ];
    }
}
