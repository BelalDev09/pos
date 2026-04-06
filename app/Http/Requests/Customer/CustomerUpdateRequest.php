<?php

namespace App\Http\Requests\Customer;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CustomerUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $customerId = $this->route('customer');

        return [

            'name'          => 'nullable|string|max:255',
            'phone'         => [
                'required',
                'string',
                Rule::unique('customers', 'phone')->ignore($customerId),
            ],
            'email'         => 'nullable|email|max:255',
            'address'       => 'nullable|string',
            'loyalty_points' => 'nullable|integer|min:0',
        ];
    }

    public function messages(): array
    {
        return [
            'restaurant_id.required' => 'Please select a restaurant.',
            'restaurant_id.exists'   => 'Selected restaurant does not exist.',
            'phone.required'         => 'Phone is required.',
            'phone.unique'           => 'Phone number already exists.',
        ];
    }
}
