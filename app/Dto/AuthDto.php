<?php

namespace App\Dto;

use App\Http\Resources\UserResource;

class AuthDto
{
    public function __construct(
        public bool $success,
        public ?string $access_token = null,
        public ?string $token_type = null,
        public ?UserResource $user = null,
        public ?string $message = null,
        public ?array $errors = null
    ) {}

    public function toArray(): array
    {
        return [
            'success' => $this->success,
            'access_token' => $this->access_token,
            'token_type' => $this->token_type,
            'user' => $this->user,
            'message' => $this->message,
            'errors' => $this->errors,
        ];
    }
}
