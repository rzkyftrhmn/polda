<?php

namespace App\Interfaces;

use App\Models\User;

interface AuthRepositoryInterface
{
    /**
     * Find a user by login identifier (email or username).
     */
    public function findByIdentifier(string $identifier): ?User;

    /**
     * Verify a plain password against the user's hashed password.
     */
    public function verifyPassword(User $user, string $password): bool;
}