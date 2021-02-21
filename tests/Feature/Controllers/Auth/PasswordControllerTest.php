<?php

namespace Tests\Feature\Controllers\Auth;

use App\Exceptions\InvalidResetTokenException;
use App\Services\PasswordResetService;
use Illuminate\Support\Str;
use Tests\TestCase;

class PasswordControllerTest extends TestCase
{

    protected const EMAIL = 'test@test.com';

    /**
     * @test
     */
    public function it_should_return_email_sent_response_when_user_is_from_matchmaker()
    {
        $mockService = $this->setUpPasswordResetService();

        $mockService
            ->shouldReceive('sendResetEmail')
            ->with(self::EMAIL);

        $this->json('POST', '/password/forgot', [
            'email' => self::EMAIL
        ])->assertSuccessful();
    }


    /**
     * @test
     */
    public function send_email_should_fail_validation_if_params_are_missing()
    {
        $this->json('POST', '/password/forgot')
            ->assertStatus(422);
    }


    /**
     * @test
     */
    public function send_email_should_fail_validation_when_email_is_not_correct()
    {
        $email = 'bobatmatchmakerdotfm';

        $this->json('POST', '/password/forgot', [
            'email' => $email
        ])->assertStatus(422);
    }


    /**
     * @test
     */
    public function it_should_return_home_redirect_after_password_reset()
    {
        $token = Str::uuid()->toString();
        $mockService = $this->setUpPasswordResetService();

        $mockService
            ->shouldReceive('resetPasswordByToken')
            ->with($token, 'supersecure');

        $response = $this->json('POST', '/password/reset', [
            'token' => $token,
            'password' => 'supersecure',
        ])->assertSuccessful();

        $this->assertEquals(env('APP_URL') . '/', $response->decodeResponseJson()['redirect']);
    }

    /**
     * @test
     */
    public function it_should_return_404_when_a_token_is_invalid()
    {
        $token = Str::uuid()->toString();
        $mockService = $this->setUpPasswordResetService();

        $mockService
            ->shouldReceive('resetPasswordByToken')
            ->with($token, 'supersecure')
            ->andThrow(InvalidResetTokenException::class, 'This reset token is not valid', 404);

        $this->json('POST', '/password/reset', [
            'token' => $token,
            'password' => 'supersecure',
        ])->assertStatus(404);
    }

    /**
     * @test
     */
    public function reset_password_should_fail_validation_if_params_are_missing()
    {
        $this->json('POST', '/password/reset')
            ->assertStatus(422);
        $this->assertGuest();
    }

    /**
     * @test
     */
    public function reset_password_should_fail_validation_when_password_is_invalid()
    {
        $this->json('POST', '/password/reset', [
            'token' => Str::uuid()->toString(),
            'password' => '1234',
        ])->assertStatus(422);

        $this->assertGuest();
    }

    protected function setUpPasswordResetService()
    {
        $mockService = \Mockery::mock(PasswordResetService::class);
        $this->app->instance(PasswordResetService::class, $mockService);

        return $mockService;
    }
}
