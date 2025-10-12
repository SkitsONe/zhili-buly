<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class AuthRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $isRegister = $this->route()->getName() === 'register';

        $rules = [
            'email' => 'required|email',
        ];

        if ($isRegister) {
            $rules['name'] = 'required|string|max:255';
            $rules['email'] = 'required|email|unique:users';
            $rules['password'] = [
                'required',
                'string',
                Password::min(8)
                    ->letters()
                    ->mixedCase()
                    ->numbers()
                    ->symbols()
                    ->uncompromised(),
            ];
        } else {
            $rules['password'] = 'required|string';
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'email.required' => 'Email обязателен для заполнения',
            'email.email' => 'Некорректный формат email',
            'email.unique' => 'Пользователь с таким email уже существует',
            'name.required' => 'Имя обязательно для заполнения',
            'password.required' => 'Пароль обязателен для заполнения',
            'password.confirmed' => 'Пароли не совпадают',
        ];
    }
}
