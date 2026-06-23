<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CustomerRequest extends FormRequest
{
    public function authorize() { return true; }

    public function rules()
    {
        $customerId = $this->route('customer');
        return [
            'customer_name' => 'required|string|max:200',
            'phone' => 'nullable|string|max:50',
            'address' => 'nullable|string|max:500',
            'email' => ['nullable', 'email', 'max:100', Rule::unique('customers', 'email')->ignore($customerId)],
            'credit_limit' => 'required|numeric|min:0',
            'is_active' => 'boolean',
        ];
    }
}