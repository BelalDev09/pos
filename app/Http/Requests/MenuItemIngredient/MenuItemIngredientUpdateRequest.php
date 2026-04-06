<?php

namespace App\Http\Requests\MenuItemIngredient;

use Illuminate\Foundation\Http\FormRequest;

class MenuItemIngredientUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'menu_item_id' => 'required|exists:menu_items,id',
            'ingredient_id' => 'required|exists:ingredients,id',
            'quantity_per_unit' => 'required|string|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'menu_item_id.required' => 'Menu Item select করতে হবে।',
            'menu_item_id.exists' => 'Selected Menu Item valid নয়।',
            'ingredient_id.required' => 'Ingredient select করতে হবে।',
            'ingredient_id.exists' => 'Selected Ingredient valid নয়।',
            'quantity_per_unit.required' => 'Quantity per unit লিখতে হবে।',
            'quantity_per_unit.max' => 'Quantity per unit সর্বাধিক 255 character হতে পারে।',
        ];
    }
}
