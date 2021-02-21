<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Exceptions\InvalidResetTokenException;
use App\Jobs\InvalidateResetToken;
use App\Models\PasswordResetToken;
use App\Models\User;
use App\Notifications\PasswordResetEmail;
use App\Repositories\User\PasswordResetRepositoryInterface;
use App\Repositories\User\UserRepositoryInterface;
use App\Services\PasswordResetService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;
use Tests\Utils\Repositories\PasswordResetInMemoryRepository;
use Tests\Utils\Repositories\UserInMemoryRepository;

class PasswordResetServiceTest extends TestCase
{
    public const EMAIL = 'test@test.com';

    protected User $user;

    protected PasswordResetService $resetService;

    protected UserRepositoryInterface $userRepository;

    protected PasswordResetRepositoryInterface $resetRepository;

    public function setUp(): void
    {
        parent::setUp();

        $this->userRepository = new UserInMemoryRepository();
        $this->resetRepository = new PasswordResetInMemoryRepository();

        $this->user = User::create(self::EMAIL, 'supersecure');
        $this->userRepository->save($this->user);

        $this->resetService = new PasswordResetService($this->userRepository, $this->resetRepository);

    }

    /** @test */
    public function send_reset_email_queues_reset_email_for_user()
    {
        $knownDate = Carbon::create(2001, 5, 21, 12);
        Carbon::setTestNow($knownDate);

        Notification::fake();
        $this->resetService->sendResetEmail(self::EMAIL);

        $token = $this->resetRepository->getTokenByUserId($this->user->getId());

        $this->assertNotNull($token);

        Notification::assertSentTo($this->user, PasswordResetEmail::class);
        Queue::assertPushed(InvalidateResetToken::class, function ($job) use ($knownDate) {
            return $job->delay == $knownDate->addDay();
        });

    }

    /** @test */
    public function send_reset_throws_error_if_user_does_not_exist()
    {
        $this->expectException(ModelNotFoundException::class);
        $this->resetService->sendResetEmail('notavalidemail@email.com');
    }

    /**
     * @test
     */
    public function it_should_reset_password_by_token_when_token_is_valid()
    {
        $token = $this->createResetToken($this->user->id);
        $this->resetService->resetPasswordByToken($token->getToken(), 'differentPassword');

        $freshUser = $this->userRepository->getUserById($this->user->getId());
        $this->assertTrue(Hash::check('differentPassword', $freshUser->password));
    }


    /**
     * @test
     * @throws \App\Exceptions\InvalidResetTokenException
     */
    public function it_should_throw_invalid_reset_token_exception()
    {
        $this->expectException(InvalidResetTokenException::class);
        $this->resetService->resetPasswordByToken('invalidtoken', 'supersecure');
    }

    protected function createResetToken(string $userId): PasswordResetToken
    {
        $token = PasswordResetToken::create($userId);
        $this->resetRepository->save($token);
        return $token;
    }
}
