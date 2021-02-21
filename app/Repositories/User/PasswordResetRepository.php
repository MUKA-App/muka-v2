<?php

namespace App\Repositories\User;

use App\Models\PasswordResetToken;
use Illuminate\Support\Facades\DB;

class PasswordResetRepository implements PasswordResetRepositoryInterface
{

    public function save(PasswordResetToken $token): void
    {
        $token->save();
    }

    public function getTokenById(string $id): ?PasswordResetToken
    {
        return PasswordResetToken::where('id', '=', $id)->first();
    }

    public function deleteById(string $id): void
    {
        DB::table('password_resets')->delete($id);
    }
}