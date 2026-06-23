<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ExpenseRequest extends FormRequest
{
    public function authorize() { return true; }

    public function rules()
    {
        return [
            'expense_date' => 'required|date',
            'expense_type_id' => 'required|exists:expense_types,id',
            'description' => 'required|string|max:500',
            'amount' => 'required|numeric|min:0',
            'attachment' => 'nullable|file|max:2048|mimes:pdf,jpg,jpeg,png',
        ];
    }
}