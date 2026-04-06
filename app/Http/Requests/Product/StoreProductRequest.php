<?php

namespace App\Http\Requests\Product;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('products.create');
    }

    public function rules(): array
    {
        return [
            'name'                 => 'required|string|max:255',
            'category_id'          => 'nullable|exists:categories,id',
            'brand_id'             => 'nullable|exists:brands,id',
            'tax_rate_id'          => 'nullable|exists:tax_rates,id',
            'sku'                  => 'nullable|string|max:100',
            'barcode'              => 'nullable|string|max:100',
            'description'          => 'nullable|string',
            'image'                => 'nullable|image|max:2048',
            'cost_price'           => 'required|numeric|min:0',
            'selling_price'        => 'required|numeric|min:0',
            'wholesale_price'      => 'nullable|numeric|min:0',
            'min_selling_price'    => 'nullable|numeric|min:0',
            'unit'                 => 'required|string|max:30',
            'track_stock'          => 'boolean',
            'allow_negative_stock' => 'boolean',
            'has_variants'         => 'boolean',
            'is_active'            => 'boolean',
            'is_pos_visible'       => 'boolean',
            'product_type'         => 'required|in:standard,service,composite',
            'track_expiry'         => 'boolean',
            'track_batch'          => 'boolean',

            // Variants
            'variants'                    => 'nullable|array',
            'variants.*.name'             => 'required_with:variants|string|max:255',
            'variants.*.sku'              => 'nullable|string|max:100',
            'variants.*.barcode'          => 'nullable|string|max:100',
            'variants.*.selling_price'    => 'nullable|numeric|min:0',
            'variants.*.cost_price'       => 'nullable|numeric|min:0',
            'variants.*.price_adjustment' => 'nullable|numeric',
            'variants.*.attributes'       => 'nullable|array',
            'variants.*.is_default'       => 'nullable|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required'          => 'Product name is required.',
            'selling_price.required' => 'Selling price is required.',
            'cost_price.required'    => 'Cost price is required.',
        ];
    }
}
