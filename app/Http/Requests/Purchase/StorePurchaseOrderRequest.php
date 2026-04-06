<?php

namespace App\Http\Requests\Purchase;

use Illuminate\Foundation\Http\FormRequest;

class StorePurchaseOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('purchases.create');
    }

    public function rules(): array
    {
        return [
            'store_id'               => 'required|exists:stores,id',
            'supplier_id'            => 'required|exists:suppliers,id',
            'expected_delivery_date' => 'nullable|date|after:today',
            'payment_terms'          => 'required|in:immediate,net_7,net_15,net_30,net_60',
            'notes'                  => 'nullable|string|max:2000',
            'supplier_reference'     => 'nullable|string|max:200',

            'items'                           => 'required|array|min:1',
            'items.*.product_id'              => 'required|exists:products,id',
            'items.*.product_variant_id'      => 'nullable|exists:product_variants,id',
            'items.*.quantity_ordered'        => 'required|numeric|min:0.001',
            'items.*.unit_cost'               => 'required|numeric|min:0',
            'items.*.tax_rate'                => 'nullable|numeric|min:0|max:100',
            'items.*.discount_rate'           => 'nullable|numeric|min:0|max:100',
            'items.*.batch_number'            => 'nullable|string|max:100',
            'items.*.expiry_date'             => 'nullable|date|after:today',
        ];
    }
}
