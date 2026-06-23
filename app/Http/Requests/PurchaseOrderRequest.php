<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PurchaseOrderRequest extends FormRequest
{
    public function authorize() { return true; }

    public function rules()
    {
        return [
            'date' => 'required|date',
            'supplier_id' => 'required|exists:suppliers,id',
            'purchase_type' => 'required|in:cash,credit',
            'payment_method' => 'nullable|string|max:50',
            'due_date' => 'nullable|date|after_or_equal:date',
            'notes' => 'nullable|string|max:1000',
            'items' => 'required|array|min:1',
            'items.*.item_id' => 'required|exists:items,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.cost_price' => 'required|numeric|min:0',
        ];
    }

    public function messages()
    {
        return [
            'items.required' => 'At least one item is required.',
            'items.*.item_id.required' => 'Please select an item.',
            'items.*.quantity.min' => 'Quantity must be at least 1.',
        ];
    }
}