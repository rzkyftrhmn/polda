<?php

namespace App\Services;

use App\Interfaces\AuthRepositoryInterface;
use App\Models\User;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

class AuthService
{
    public function __construct(private AuthRepositoryInterface $repo)
    {
    }

    /**
     * Attempt login and return array with user and token.
     *
     * @return array{user: array, token: string}|null
     */
    public function login(string $identifier, string $password): ?array
    {
        // Build credentials for JWT attempt
        if (filter_var($identifier, FILTER_VALIDATE_EMAIL)) {
            $credentials = ['email' => $identifier, 'password' => $password];
        } elseif (Schema::hasColumn('users', 'username')) {
            $credentials = ['username' => $identifier, 'password' => $password];
        } else {
            $credentials = ['email' => $identifier, 'password' => $password];
        }

        if (!$token = Auth::guard('api')->attempt($credentials)) {
            return null;
        }

        /** @var User $user */
        $user = Auth::guard('api')->user();

        return [
            'user' => $user->toLoginPayload(),
            'token' => $token,
        ];
    }

    /**
     * Invalidate a JWT token (logout).
     */
    public function logout(string $token): bool
    {
        if (!$token) {
            return false;
        }
        try {
            JWTAuth::setToken($token)->invalidate();
            return true;
        } catch (\Throwable $e) {
            return false;
        }
    }
}