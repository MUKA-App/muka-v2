<?php

namespace Tests\Utils\Repositories;

use App\Models\User;
use App\Repositories\User\UserRepositoryInterface;

class UserInMemoryRepository implements UserRepositoryInterface
{
    /** @var User[] */
    private array $users = [];

    public function save(User $user): void
    {
        $this->users[] = $user;
    }

    public function getUserById(string $id): ?User
    {
        foreach ($this->users as $user) {
            if ($user->getId() === $id) {
                return $user;
            }
        }
        return null;
    }

    public function getUserByEmail(string $email): ?User
    {
        foreach ($this->users as $user) {
            if ($user->getEmail() === $email) {
                return $user;
            }
        }
        return null;
    }

    public function getUserByVerificationToken(string $token): ?User
    {
        foreach ($this->users as $user) {
            if ($user->verify_token === $token) {
                return $user;
            }
        }
        return null;
    }
}
