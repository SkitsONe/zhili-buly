<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePostRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => 'sometimes|required|string|max:255',
            'content' => 'sometimes|required|string',
            'short_description' => 'nullable|string|max:500',
            'category_id' => 'nullable|integer', // делаем необязательным
            'category_name' => 'nullable|string|max:255', // добавляем поле для имени категории
            'published' => 'boolean',
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
