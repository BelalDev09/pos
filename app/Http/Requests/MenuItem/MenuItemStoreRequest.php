<?php

namespace App\Http\Requests\MenuItem;

use Illuminate\Foundation\Http\FormRequest;

class MenuItemStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'category_id' => 'nullable|exists:categories,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',

            'base_price' => 'required|numeric|min:0',
            'cost_price' => 'nullable|numeric|min:0',

            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',

            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpg,jpeg,png|max:2048',

            // 'stock_quantity' => 'nullable|numeric|min:0',

            // flexible validation
            'sizes' => 'nullable|array',
            'sizes.*.name' => 'nullable|string|max:50',
            'sizes.*.price' => 'nullable|numeric|min:0',

            'ingredients' => 'nullable|array',
            'ingredients.*.name' => 'nullable|string|max:255',
            'ingredients.*.qty' => 'nullable|string|max:255',
            'ingredients.*.price' => 'nullable|numeric|min:0',
        ];
    }
}
