<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\AuthService;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function __construct(private AuthService $service)
    {
    }

    /**
     * POST /api/v1/login
     */
    public function login(Request $request)
    {
        $validated = $request->validate([
            'username' => ['required','string'],
            'password' => ['required','string'],
        ]);

        $result = $this->service->login($validated['username'], $validated['password']);
        if (!$result) {
            return response()->json(format_error('Invalid credentials'), 401);
        }

        return response()->json(format_success('Login successfully', $result));
    }

    /**
     * POST /api/v1/logout
     */
    public function logout(Request $request)
    {
        $token = $request->bearerToken();
        if (!$token) {
            return response()->json(format_error('Token not provided'), 400);
        }

        $ok = $this->service->logout($token);
        if (!$ok) {
            return response()->json(format_error('Invalid token'), 400);
        }
        return response()->json(format_success('Logout successful', []));
    }
}