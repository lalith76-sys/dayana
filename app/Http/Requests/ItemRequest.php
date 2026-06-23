<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ItemRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $itemId = $this->route('item');
        
        $rules = [
            'category_id' => 'required|exists:categories,id',
            'item_name' => 'required|string|max:200',
            'brand' => 'nullable|string|max:100',
            'barcode' => [
                'nullable',
                'string',
                'max:100',
                Rule::unique('items', 'barcode')->ignore($itemId),
            ],
            'cost_price' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0|gte:cost_price',
            'stock_quantity' => 'required|integer|min:0',
            'reorder_level' => 'required|integer|min:0',
            'status' => 'required|in:active,inactive,discontinued',
            'description' => 'nullable|string|max:500',
        ];

        return $rules;
    }

    public function messages()
    {
        return [
            'selling_price.gte' => 'Selling price must be greater than or equal to cost price.',
            'category_id.required' => 'Please select a category.',
        ];
    }
}