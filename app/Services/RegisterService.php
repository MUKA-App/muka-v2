<?php

declare(strict_types=1);

namespace App\Services;

use App\Exceptions\DuplicateUserRegistrationException;
use App\Exceptions\UserVerificationException;
use App\Models\User;
use App\Notifications\EmailVerification;
use App\Repositories\User\UserRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Auth;

class RegisterService
{
    private UserRepositoryInterface $userRepository;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * @param string $email
     * @param string $password
     * @return User
     * @throws DuplicateUserRegistrationException
     */
    public function register(string $email, string $password): User
    {
        $user = $this->userRepository->getUserByEmail($email);

        if ($user) {
            throw new DuplicateUserRegistrationException();
        }

        $user = User::create($email, $password);
        $this->userRepository->save($user);
        $this->sendVerificationEmail($email);
        return $user;
    }

    /**
    /**
     * @param string $token
     * @throws UserVerificationException
     */
    public function verify(string $token): void
    {
        $user = $this->userRepository->getUserByVerificationToken($token);

        if (!$user) {
            throw new UserVerificationException('Invalid token', 404);
        }

        $user->verify();
        $this->userRepository->save($user);

        Auth::login($user);
    }

    /**
     * Send verification email if user not verified
     * @param string $email
     * @throws UserVerificationException
     */
    public function sendVerificationEmail(string $email): void
    {
        $user = $this->userRepository->getUserByEmail($email);

        if (!$user) {
            throw new ModelNotFoundException("A user with this email can't be found", 404);
        }

        if ($user->isVerified()) {
            throw new UserVerificationException('Email already verified');
        }

        $user->notify(new EmailVerification());
    }
}
