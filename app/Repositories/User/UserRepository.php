<?php

namespace App\Repositories\User;

use App\Models\User;

class UserRepository implements UserRepositoryInterface
{
    public function save(User $user): void
    {
        $user->save();
    }

    public function getUserById(string $id): ?User
    {
        return User::from('users')
            ->where('id', '=', $id)
            ->first();
    }

    public function getUserByEmail(string $email): ?User
    {
        return User::from('users')
            ->where('email', '=', $email)
            ->first();
    }

    public function getUserByVerificationToken(string $token): ?User
    {
        return User::from('users')
            ->where('verify_token', '=', $token)
            ->first();
    }
}
