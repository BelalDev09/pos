<?php

namespace App\Http\Requests\Order;

use Illuminate\Foundation\Http\FormRequest;

class OrderCreateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'order_number'  => 'required|string|unique:orders,order_number',
            'table_id'      => 'nullable|exists:restaurant_tables,id',
            'customer_id'   => 'nullable|exists:users,id',
            'waiter_id'     => 'nullable|exists:users,id',
            'order_type'    => 'required|in:eat_in,collection,delivery,qr_self',
            'status'        => 'nullable|in:pending,preparing,ready,served,completed,cancelled',
            'kitchen_status' => 'nullable|in:pending,in_progress,completed',
            'payment_status' => 'nullable|in:pending,partial,paid,refunded',
            'sub_total'     => 'nullable|numeric|min:0',
            'discount_amount' => 'nullable|numeric|min:0',
            'tax_amount'    => 'nullable|numeric|min:0',
            'service_charge' => 'nullable|numeric|min:0',
            'grand_total'   => 'required|numeric|min:0',
            'loyalty_points_earned' => 'nullable|integer|min:0',
        ];
    }

    public function messages(): array
    {
        return [
            'restaurant_id.required' => 'Please select a restaurant.',
            'restaurant_id.exists' => 'Selected restaurant does not exist.',
            'order_number.required' => 'Order number is required.',
            'order_number.unique' => 'This order number already exists.',
            'order_type.required' => 'Please select order type.',
            'grand_total.required' => 'Grand total is required.',
        ];
    }
}
