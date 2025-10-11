<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
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

    public function login(Request $request): JsonResponse
    {
        try {
            $credentials = $request->validate([
                'email' => 'required|email',
                'password' => 'required',
            ]);

            $authData = $this->authService->login($credentials);

            return response()->json([
                'success' => true,
                'access_token' => $authData['access_token'],
                'token_type' => $authData['token_type'],
                'user' => $authData['user'],
            ]);

        } catch (ValidationException $e) {
            Log::error($e->getMessage());
            abort(401, );

        } catch (Exception $e) {
            Log::error($e->getMessage());
            abort(500, 'Error when logging in');
        }
    }

    public function register(Request $request): JsonResponse
    {
        try {
            $data = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:8|',
            ]);

            $authData = $this->authService->register($data);

            return response()->json([
                'success' => true,
                'access_token' => $authData['access_token'],
                'token_type' => $authData['token_type'],
                'user' => $authData['user'],
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
            $user = $this->authService->getCurrentUser($request->user());

            return response()->json([
                'success' => true,
                'user' => $user
            ]);

        } catch (Exception $e) {
            Log::error($e->getMessage());
            abort(500, 'Error when receiving user data');
        }
    }
}
