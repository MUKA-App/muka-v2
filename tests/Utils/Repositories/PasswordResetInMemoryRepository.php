<?php


namespace Tests\Utils\Repositories;


use App\Models\PasswordResetToken;
use App\Repositories\User\PasswordResetRepositoryInterface;

class PasswordResetInMemoryRepository implements PasswordResetRepositoryInterface
{
    /** @var PasswordResetToken[] */
    protected array $tokens = [];

    public function save(PasswordResetToken $token): void
    {
        $this->tokens[] = $token;
    }

    public function getTokenById(string $id): ?PasswordResetToken
    {
        foreach ($this->tokens as $token) {
            if ($token->getToken() === $id) {
                return $token;
            }
        }
        return null;
    }

    public function deleteById(string $id): void
    {
        foreach ($this->tokens as $key => $token) {
            if ($token->getToken() === $id) {
                unset($this->tokens[$key]);
            }
        }
    }


    public function getTokenByUserId(string $userID): ?PasswordResetToken
    {
        foreach ($this->tokens as $token) {
            if ($token->getUserId() === $userID) {
                return $token;
            }
        }
        return null;
    }
}