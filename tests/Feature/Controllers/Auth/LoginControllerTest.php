<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Auth;

use App\Models\User;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class LoginControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
    * @test
    */
    public function it_logs_in_a_user_by_email_and_password_and_redirects_to_home()
    {
        $email = 'test@test.com';
        $user = User::factory()->create([
            'email' => $email,
            'password' => $this->getPasswordHash(),

        ]);

        $response = $this->json('POST', '/login', [
            'email' => $email,
            'password' => 'passw0rd',
            'remember_token' => true,
        ])->assertSuccessful();

        $this->assertAuthenticatedAs($user);
        $data = $response->decodeResponseJson();
        $this->assertEquals(env('APP_URL') . '/home', $data['redirect']);
    }

    /**
     * @test
     */
    public function it_throws_user_not_found_error_when_user_doesnt_exist()
    {
        $email = 'test@test.com';

        $this->json('POST', '/login', [
            'email' => $email,
            'password' => 'passw0rd',
            'remember_token' => true,
        ])->assertStatus(404);

    }

    /**
     * @test
     */
    public function it_throws_incorrect_password_exception_when_password_doesnt_match()
    {
        $email = 'test@test.com';
        $user = User::factory()->create([
            'email' => $email,
            'password' => $this->getPasswordHash(),
        ]);

        $this->json('POST', '/login', [
            'email' => $email,
            'password' => 'notthepassword',
            'remember_token' => true,
        ])->assertStatus(401);

    }


    /**
     * @test
     */
    public function it_gets_422_code_when_email_is_not_in_request()
    {

        $this->json('POST', '/login', [
            'recaptcha_token' => 'testytoken',
            'password' => 'passw0rd',
            'remember_token' => true,
        ])->assertStatus(422);
    }

    /**
     * @test
     */
    public function it_gets_422_code_when_remember_token_is_not_in_request()
    {
        $email = 'test@test.com';
        $this->json('POST', '/login', [
            'recaptcha_token' => 'testytoken',
            'password' => 'passw0rd',
            'email' => $email
        ])->assertStatus(422);
    }


    /**
     * Returns the hash of passw0rd
     * @return string
     */
    protected function getPasswordHash(): string
    {
        return '$2y$04$zp5F/S4ncbzKzyEdCWsna.ml6oKFi5MKNtz6OJUd3S.9huvi.vDmu';
    }
}
