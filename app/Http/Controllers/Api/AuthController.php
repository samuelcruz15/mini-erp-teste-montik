<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class AuthController extends Controller
{
    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string'
        ]);

        $credentials = $request->only(['email', 'password']);

        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            $token = $user->createToken('API Token')->accessToken;

            return response()->json([
                'success' => true,
                'message' => 'Login realizado com sucesso',
                'data' => [
                    'user' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'email' => $user->email
                    ],
                    'token' => $token,
                    'token_type' => 'Bearer',
                    'expires_in' => 86400 // 24 horas
                ]
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Credenciais inválidas'
        ], 401);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->token()->revoke();

        return response()->json([
            'success' => true,
            'message' => 'Logout realizado com sucesso'
        ]);
    }

    public function profile(Request $request): JsonResponse
    {
        $user = $request->user();
        $user->load('profile');

        return response()->json([
            'success' => true,
            'data' => [
                'user' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'profile' => $user->profile
                ]
            ]
        ]);
    }

    public function refresh(Request $request): JsonResponse
    {
        $user = $request->user();
        
        $request->user()->token()->revoke();
        
        $newToken = $user->createToken('API Token')->accessToken;

        return response()->json([
            'success' => true,
            'message' => 'Token renovado com sucesso',
            'data' => [
                'token' => $newToken,
                'token_type' => 'Bearer',
                'expires_in' => 86400
            ]
        ]);
    }
} 