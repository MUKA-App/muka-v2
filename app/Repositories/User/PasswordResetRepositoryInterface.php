<?php

namespace App\Repositories\User;

use App\Models\PasswordResetToken;

interface PasswordResetRepositoryInterface
{
    public function save(PasswordResetToken $token): void;

    public function getTokenById(string $id): ?PasswordResetToken;

    public function deleteById(string $id): void;
}
