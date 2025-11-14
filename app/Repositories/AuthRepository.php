<?php

namespace App\Repositories;

use App\Interfaces\AuthRepositoryInterface;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

class AuthRepository implements AuthRepositoryInterface
{
    public function findByIdentifier(string $identifier): ?User
    {
        // If identifier looks like an email, prefer email
        if (filter_var($identifier, FILTER_VALIDATE_EMAIL)) {
            return User::where('email', $identifier)->first();
        }

        // Otherwise try username if column exists, fallback to email
        if (Schema::hasColumn('users', 'username')) {
            $user = User::where('username', $identifier)->first();
            if ($user) return $user;
        }

        return User::where('email', $identifier)->first();
    }

    public function verifyPassword(User $user, string $password): bool
    {
        return Hash::check($password, $user->password);
    }
}