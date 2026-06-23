<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CategoryRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $rules = [
            'name' => [
                'required',
                'string',
                'max:100',
                Rule::unique('categories', 'name')->ignore($this->route('category')),
            ],
            'description' => 'nullable|string|max:500',
            'is_active' => 'boolean',
        ];

        return $rules;
    }

    public function messages()
    {
        return [
            'name.required' => 'The category name is required.',
            'name.unique' => 'This category name already exists.',
        ];
    }
}