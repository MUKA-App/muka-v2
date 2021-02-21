<?php

namespace App\Services;

use App\Exceptions\InvalidResetTokenException;
use App\Jobs\InvalidateResetToken;
use App\Models\PasswordResetToken;
use App\Notifications\PasswordResetEmail;
use App\Repositories\User\PasswordResetRepositoryInterface;
use App\Repositories\User\UserRepositoryInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Auth;

class PasswordResetService
{
    protected UserRepositoryInterface $userRepository;

    private PasswordResetRepositoryInterface $resetRepository;

    public function __construct(
        UserRepositoryInterface $userRepository,
        PasswordResetRepositoryInterface $resetRepository
    ) {
        $this->userRepository = $userRepository;
        $this->resetRepository = $resetRepository;
    }

    /**
     * Generates a token for the password reset and stores it in the password_resets table
     * @param string $email
     */
    public function sendResetEmail(string $email): void
    {
        $user = $this->userRepository->getUserByEmail($email);
        if (!$user) {
            throw new ModelNotFoundException("A user with this email can't be found", 404);
        }

        $token = PasswordResetToken::create($user->getId());
        $this->resetRepository->save($token);

        $user->notify(new PasswordResetEmail($token->getToken()));

        InvalidateResetToken::dispatch($token)->delay(now()->addDay());
    }

    /**
     * @param string $token
     * @param string $password
     * @throws InvalidResetTokenException
     */
    public function resetPasswordByToken(string $token, string $password)
    {
        $tokenModel = $this->resetRepository->getTokenById($token);

        if (!isset($tokenModel)) {
            throw new InvalidResetTokenException();
        }

        $user = $this->userRepository->getUserById($tokenModel->getUserId());

        if (!$user) {
            throw new ModelNotFoundException("A user with this email can't be found", 404);
        }
        $user->resetPassword($password);
        $this->userRepository->save($user);

        $this->resetRepository->deleteById($token);

        Auth::login($user);
    }
}
