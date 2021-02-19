<?php

namespace App\Repositories\User;

use App\Models\User;

interface UserRepositoryInterface
{
    public function save(User $user): void;

    public function getUserById(string $id): ?User;

    public function getUserByEmail(string $email): ?User;

    public function getUserByVerificationToken(string $token): ?User;
}
