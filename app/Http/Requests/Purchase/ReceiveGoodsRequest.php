<?php

namespace App\Http\Requests\Purchase;

use Illuminate\Foundation\Http\FormRequest;

class ReceiveGoodsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('purchases.receive');
    }

    public function rules(): array
    {
        return [
            'items'                                => 'required|array|min:1',
            'items.*.purchase_order_item_id'       => 'required|exists:purchase_order_items,id',
            'items.*.quantity_received'            => 'required|numeric|min:0.001',
        ];
    }
}
