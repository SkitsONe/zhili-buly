<?php

namespace App\Http\Controllers\API;

use App\Dto\LoginDto;
use App\Dto\RegisterDto;
use App\Http\Controllers\Controller;
use App\Http\Requests\AuthRequest;
use App\Services\AuthService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function __construct(
        private AuthService $authService
    ) {}

    public function login(AuthRequest $request): JsonResponse
    {
        try {
            $credentials = LoginDto::fromRequest($request->validate([
                'email' => 'required|email',
                'password' => 'required',
            ]));

            $authDto = $this->authService->login($credentials);

            return response()->json([
                'success' => true,
                'access_token' => $authDto->access_token,
                'token_type' => $authDto->token_type,
                'user' => $authDto->user,
            ]);

        } catch (ValidationException $e) {
            Log::error($e->getMessage());
            abort(401);

        } catch (Exception $e) {
            Log::error($e->getMessage() . $e->getFile() . $e->getLine());
            abort(500, 'Error when logging in');
        }
    }

    public function register(AuthRequest $request): JsonResponse
    {
        try {
            $data = RegisterDto::fromRequest($request->validated());

            $authDto = $this->authService->register($data);

            return response()->json([
                'success' => true,
                'access_token' => $authDto->access_token,
                'token_type' => $authDto->token_type,
                'user' => $authDto->user,
            ], 201);

        } catch (Exception $e) {
            Log::error($e->getMessage());
            abort(500, 'Error in registration');
        }
    }

    public function logout(Request $request): JsonResponse
    {
        try {
            $this->authService->logout($request->user());

            return response()->json([
                'success' => true,
            ]);

        } catch (Exception $e) {
            Log::error($e->getMessage());
            abort(500, 'Error in logout');
        }
    }

    public function me(Request $request): JsonResponse
    {
        try {
            $authDto = $this->authService->getCurrentUser($request->user());

            return response()->json([
                'success' => true,
                'user' => $authDto->user
            ]);

        } catch (Exception $e) {
            Log::error($e->getMessage());
            abort(500, 'Error when receiving user data');
        }
    }
}
