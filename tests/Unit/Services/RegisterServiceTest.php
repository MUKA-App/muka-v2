<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Exceptions\DuplicateUserRegistrationException;
use App\Exceptions\UserVerificationException;
use App\Models\User;
use App\Notifications\EmailVerification;
use App\Repositories\User\ProfileRepositoryInterface;
use App\Services\RegisterService;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;
use Tests\Utils\Repositories\ProfileInMemoryRepository;

class RegisterServiceTest extends TestCase
{

    protected ProfileRepositoryInterface $userRepository;
    protected RegisterService $service;

    public function setUp(): void
    {
        parent::setUp();
        $this->userRepository = new ProfileInMemoryRepository();
        $this->service = new RegisterService($this->userRepository);
    }


    /** @test
     * @throws DuplicateUserRegistrationException
     */
    public function registration_works_and_creates_new_user()
    {
        $this->withoutEvents();
        $user = $this->service->register('bob@matchmaker.fm', 'supersecure');

        $this->assertNotNull($user);
    }

    /** @test
     */
    public function registration_fails_when_user_with_email_already_exists()
    {
        $user = User::factory()->make([
            'email' => 'test@test.com'
        ]);
        $this->userRepository->save($user);

        $this->expectException(DuplicateUserRegistrationException::class);
        $this->service->register('test@test.com', 'supersecure');
    }


    /** @test */
    public function verification_method_verifies_user_and_sets_token_to_null_and_email_verified_to_now()
    {
        $knownDate = Carbon::create(2001, 5, 21, 12, 41, 28);
        Carbon::setTestNow($knownDate);

        $user = User::factory()->unverified()->make([
            'email' => 'test@test.com',
            'verify_token' => 'verificationtoken'
        ]);
        $this->userRepository->save($user);

        $this->service->verify('verificationtoken');

        $freshUser = $this->userRepository->getUserByEmail('test@test.com');

        $this->assertEquals($knownDate, $freshUser->getVerifiedAt());
        $this->assertNull($freshUser->verify_token);

        $this->assertAuthenticatedAs($user);
    }

    /** @test */
    public function verification_method_throws_invalid_verification_token_exception()
    {
        $user = User::factory()->unverified()->make([
            'email' => 'test@test.com',
            'verify_token' => 'verificationtoken'
        ]);
        $this->userRepository->save($user);


        $this->expectException(UserVerificationException::class);
        $this->service->verify('notvalid');
    }


    /** @test */
    public function send_email_method_dispatches_email_notification()
    {
        $user = User::factory()->unverified()->make([
            'email' => 'test@test.com',
            'verify_token' => 'verificationtoken'
        ]);
        $this->userRepository->save($user);

        $this->service->sendVerificationEmail('test@test.com');
        Notification::assertSentTo($user, EmailVerification::class);
    }

    /** @test */
    public function send_email_method_throws_user_already_verified_exception()
    {
        $user = User::factory()->make([
            'email' => 'test@test.com'
        ]);
        $this->userRepository->save($user);

        $this->expectException(UserVerificationException::class);
        $this->service->sendVerificationEmail('test@test.com');
    }

    /** @test */
    public function send_email_method_throws_user_not_found_exception()
    {
        $this->expectException(ModelNotFoundException::class);
        $this->service->sendVerificationEmail('blah');
    }
}
