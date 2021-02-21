<?php

namespace Tests\Unit\Jobs;

use App\Jobs\InvalidateResetToken;
use App\Models\PasswordResetToken;
use App\Models\User;
use App\Repositories\User\PasswordResetRepositoryInterface;
use App\Repositories\User\UserRepositoryInterface;
use Tests\TestCase;
use Tests\Utils\Repositories\PasswordResetInMemoryRepository;
use Tests\Utils\Repositories\UserInMemoryRepository;

class InvalidateResetTokenTest extends TestCase
{
    public const EMAIL = 'test@test.com';

    protected User $user;

    protected UserRepositoryInterface $userRepository;

    protected PasswordResetRepositoryInterface $resetRepository;

    public function setUp(): void
    {
        parent::setUp();

        $this->userRepository = new UserInMemoryRepository();
        $this->resetRepository = new PasswordResetInMemoryRepository();

        $this->user = User::create(self::EMAIL, 'supersecure');
        $this->userRepository->save($this->user);
    }

    /**
     * @test
     */
    public function it_should_remove_token_on_job_firing()
    {
        $token = $this->createResetToken($this->user->id);
        $job = New InvalidateResetToken($token->getToken());
        $job->handle($this->resetRepository);

        $this->assertNull($this->resetRepository->getTokenByUserId($this->user->getId()));
    }

    protected function createResetToken(string $userId): PasswordResetToken
    {
        $token = PasswordResetToken::create($userId);
        $this->resetRepository->save($token);
        return $token;
    }
}
