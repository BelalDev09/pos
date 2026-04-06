<?php

namespace App\Http\Requests\MenuItem;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class MenuItemUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $menuItemId = $this->route('id'); //  FIXED

        return [
            'category_id' => 'nullable|exists:categories,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',

            'base_price' => 'required|numeric|min:0',
            'cost_price' => 'nullable|numeric|min:0',

            'sku' => ['nullable', Rule::unique('menu_items', 'sku')->ignore($menuItemId)],

            'preparation_time_minutes' => 'nullable|integer|min:0',

            // image
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',

            // multiple images
            'images' => 'nullable|array',
            'images.*' => 'image|mimes:jpg,jpeg,png|max:2048',

            'is_available' => 'nullable|boolean',
            'track_inventory' => 'nullable|boolean',

            // 'stock_quantity' => 'nullable|numeric|min:0',

            /*
            |--------------------------------------------------------------------------
            | Sizes (Variants)
            |--------------------------------------------------------------------------
            */
            'sizes' => 'nullable|array',
            'sizes.*.name' => 'required|string|max:50',
            'sizes.*.price' => 'required|numeric|min:0',

            /*
            |--------------------------------------------------------------------------
            | Ingredients
            |--------------------------------------------------------------------------
            */
            'ingredients' => 'nullable|array',
            'ingredients.*.name' => 'required|string|max:255',
            'ingredients.*.qty' => 'required|string|max:255',
            'ingredients.*.price' => 'required|numeric|min:0',
        ];
    }
}
