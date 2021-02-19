<?php

namespace Tests\Feature\Controllers\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class VerificationControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function verify_user_endpoint_should_return_404_if_invalid_token()
    {
        $response = $this->json('post', '/register/verify',[
            'token' => 'random_token'
        ])->assertStatus(404)
            ->decodeResponseJson();

        $this->assertEquals('Invalid token', $response['message']);
    }

    /** @test */
    public function verify_user_endpoint_returns_200()
    {
        User::factory()->unverified()->create([
            'email' => 'test@test.com',
            'verify_token' => 'verificationtoken'
        ]);

        $response = $this->json('post', '/register/verify', [
            'token' => 'verificationtoken'
        ])
            ->assertStatus(200)
            ->decodeResponseJson();

        $this->assertEquals('Email successfully validated', $response['message']);
    }

    /** @test */
    public function verify_user_endpoint_returns_422_when_data_is_missing()
    {
        $this->json('post', '/register/verify')
            ->assertStatus(422);
    }

    /** @test */
    public function resend_verification_email_endpoint_returns_200_if_resent()
    {
        User::factory()->unverified()->create([
            'email' => 'test@test.com',
            'verify_token' => 'verificationtoken'
        ]);

        $response = $this->json('post', '/register/verify/resend', [
            'recaptcha_token' => 'testytoken',
            'email' => 'test@test.com'
        ])->assertStatus(200)
            ->decodeResponseJson();

        $this->assertEquals('Verification email sent', $response['message']);
    }

    /** @test */
    public function resend_verification_email_endpoint_returns_422_if_no_data_present()
    {
        $this->json('post', '/register/verify/resend')
            ->assertStatus(422);
    }

    /** @test */
    public function resend_verification_email_endpoint_returns_404_if_no_user()
    {
        $this->json('post', '/register/verify/resend', [
            'recaptcha_token' => 'testytoken',
            'email' => 'test@test.com'
        ])->assertStatus(404);
    }


    /**
     * @test
     * @dataProvider validateFields
     */
    public function resend_verification_email_endpoint_returns_400_if_verified(string $field, $value)
    {
        $user = User::factory()->create([
            'email' => 'test@test.com',
        ]);

        $response = $this->json('post', '/register/verify/resend', [
            'recaptcha_token' => 'testytoken',
            'email' => $user->getEmail()
        ])->assertStatus(400)
            ->decodeResponseJson();

        $this->assertEquals('Email already verified', $response['message']);
    }

    public function validateFields(): array
    {
        return [
            ['oauth_provider', 'podcast'],
            ['email_verified_at', Carbon::now()]
        ];
    }
}
