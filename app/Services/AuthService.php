<?php

namespace App\Services;

use App\Repositories\UserRepository;
use Illuminate\Validation\ValidationException;

class AuthService
{
    public function __construct(
        private UserRepository $userRepository
    ) {}

    public function login(array $credentials): array
    {
        $user = $this->userRepository->findByEmail($credentials['email']);

        if (!$user || !$this->userRepository->validateCredentials($user, $credentials['password'])) {
            throw ValidationException::withMessages([
                'email' => ['Invalid credentials.'],
            ]);
        }

        $token = $this->userRepository->createAuthToken($user);

        return [
            'user' => $user,
            'access_token' => $token,
            'token_type' => 'Bearer',
        ];
    }

    public function register(array $data): array
    {
        $user = $this->userRepository->createUser($data);
        $token = $this->userRepository->createAuthToken($user);

        return [
            'user' => $user,
            'access_token' => $token,
            'token_type' => 'Bearer',
        ];
    }

    public function logout($user): bool
    {
        return $this->userRepository->deleteCurrentToken($user);
    }

    public function getCurrentUser($user)
    {
        return $user;
    }
}
