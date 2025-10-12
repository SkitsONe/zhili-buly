<?php

namespace App\Services;

use App\Dto\LoginDto;
use App\Repositories\UserRepository;
use App\Dto\AuthDto;
use App\Dto\RegisterDto;
use App\Http\Resources\UserResource;
use Illuminate\Validation\ValidationException;

class AuthService
{
    public function __construct(
        private UserRepository $userRepository
    ) {}

    public function login(LoginDto $credentials): AuthDto
    {
        $user = $this->userRepository->findByEmail($credentials->email);

        if (!$user || !$this->userRepository->validateCredentials($user, $credentials->password)) {
            throw ValidationException::withMessages([
                'email' => ['Invalid credentials.'],
            ]);
        }

        $token = $this->userRepository->createAuthToken($user);

        return new AuthDto(
            success: true,
            access_token: $token,
            token_type: 'Bearer',
            user: new UserResource($user)
        );
    }

    public function register(RegisterDto $data): AuthDto
    {
        $user = $this->userRepository->registerUser($data);

        $token = $this->userRepository->createAuthToken($user);

        return new AuthDto(
            success: true,
            access_token: $token,
            token_type: 'Bearer',
            user: new UserResource($user)
        );
    }

    public function logout($user): bool
    {
        return $this->userRepository->deleteCurrentToken($user);
    }

    public function getCurrentUser($user): AuthDto
    {
        return new AuthDto(
            success: true,
            user: new UserResource($user)
        );
    }
}
