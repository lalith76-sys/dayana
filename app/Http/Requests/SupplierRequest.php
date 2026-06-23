<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SupplierRequest extends FormRequest
{
    public function authorize() { return true; }

    public function rules()
    {
        $supplierId = $this->route('supplier');
        return [
            'supplier_name' => 'required|string|max:200',
            'address' => 'nullable|string|max:500',
            'contact_person' => 'nullable|string|max:100',
            'phone' => 'nullable|string|max:50',
            'email' => ['nullable', 'email', 'max:100', Rule::unique('suppliers', 'email')->ignore($supplierId)],
            'credit_limit' => 'required|numeric|min:0',
            'is_active' => 'boolean',
        ];
    }
}