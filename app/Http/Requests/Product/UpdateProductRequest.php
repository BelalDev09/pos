<?php

namespace App\Http\Requests\Product;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('products.update');
    }

    public function rules(): array
    {
        return [
            'name'                 => 'sometimes|required|string|max:255',
            'category_id'          => 'nullable|exists:categories,id',
            'brand_id'             => 'nullable|exists:brands,id',
            'tax_rate_id'          => 'nullable|exists:tax_rates,id',
            'sku'                  => 'nullable|string|max:100',
            'barcode'              => 'nullable|string|max:100',
            'description'          => 'nullable|string',
            'image'                => 'nullable|image|max:2048',
            'cost_price'           => 'sometimes|required|numeric|min:0',
            'selling_price'        => 'sometimes|required|numeric|min:0',
            'unit'                 => 'sometimes|required|string|max:30',
            'track_stock'          => 'boolean',
            'allow_negative_stock' => 'boolean',
            'is_active'            => 'boolean',
            'is_pos_visible'       => 'boolean',
            'product_type'         => 'sometimes|in:standard,service,composite',

            'variants'                    => 'nullable|array',
            'variants.*.id'               => 'nullable|exists:product_variants,id',
            'variants.*.name'             => 'required_with:variants|string|max:255',
            'variants.*.sku'              => 'nullable|string|max:100',
            'variants.*.barcode'          => 'nullable|string|max:100',
            'variants.*.selling_price'    => 'nullable|numeric|min:0',
            'variants.*.price_adjustment' => 'nullable|numeric',
            'variants.*.attributes'       => 'nullable|array',
            'variants.*.is_default'       => 'nullable|boolean',
        ];
    }
}
