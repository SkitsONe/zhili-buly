<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePostRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => [
                'required',
                'string',
                'max:255'
            ],
            'content' => [
                'required',
                'string'
            ],
            'short_description' => [
                'nullable',
                'string',
                'max:500'
            ],
            'category_id' => [
                'nullable',
                'integer'
            ],
            'category_name' => [
                'nullable',
                'string',
                'max:255'
            ],
            'published' => [
                'boolean'
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Title required',
            'content.required' => 'Content required',
        ];
    }

    public function prepareForValidation(): void
    {
        if ($this->has('category_name') && !$this->has('category_id')) {
            $this->merge([
                'category_name' => trim($this->category_name)
            ]);
        }
    }
}
