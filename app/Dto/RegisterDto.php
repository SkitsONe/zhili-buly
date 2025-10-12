<?php

namespace App\Dto;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class RegisterDto
{
    public function __construct(
        public string $name,
        public string $email,
        public string $password
    ) {}

    public static function fromRequest(array $data): self
    {
        return new self(
            name: $data['name'],
            email: $data['email'],
            password: Hash::make($data['password'])
        );
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'email' => $this->email,
            'password' => $this->password,
        ];
    }
}
