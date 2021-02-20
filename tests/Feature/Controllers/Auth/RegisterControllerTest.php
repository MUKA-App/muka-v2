<?php

namespace Tests\Feature\Controllers\Auth;

use App\Http\Resources\UserResource;
use App\Repositories\User\ProfileRepositoryInterface;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\App;
use Tests\TestCase;
use App\Models\User;

class RegisterControllerTest extends TestCase
{
    use RefreshDatabase;

    protected ProfileRepositoryInterface $userRepository;

    public function setUp(): void
    {
        parent::setUp();
        $this->userRepository = App::make(ProfileRepositoryInterface::class);
    }

    /**
     * @test
     */
    public function it_should_add_user_correctly()
    {
        $this->fakeEventFacade();
        $email = 'test@test.com';

        $response = $this->json('POST', '/register', [
            'email' => $email,
            'password' => 'supersecure'
        ])->assertSuccessful();

        $user = $this->userRepository->getUserByEmail($email);

        $this->assertEquals((new UserResource($user))->toArray(request()), $response->decodeResponseJson()['data']);
    }

    /**
     * @test
     * @dataProvider invalidRegistrationFields
     */
    public function registration_validation_returns_422_if_fields_invalid($data)
    {
        $this->json('post', '/register', $data)
            ->assertStatus(422);
    }

    /**
     * Returns array of invalid registration data and the expected
     */
    public function invalidRegistrationFields()
    {
        return [
            // Required
            [['email' => '', 'password' => '']],
            // Invalid email syntax & password length
            [['email' => 'invalidemail', 'password' => '1234567']],
            // Invalid types
            [['email' => 1, 'password' => 1]]
        ];
    }
}
