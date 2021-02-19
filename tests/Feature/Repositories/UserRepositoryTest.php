<?php

namespace Tests\Feature\Repositories;

use App\Models\User;
use App\Repositories\User\UserRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserRepositoryTest extends TestCase
{
    use RefreshDatabase;

    protected UserRepository $repo;

    /**
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->repo = $this->app->make(UserRepository::class);
    }

    /**
     * @test
     */
    public function it_should_save_user_to_database()
    {
        $user = User::factory()->make();

        $this->repo->save($user);

        $this->assertDatabaseHas('users', [
            'id' => $user->getId()
        ]);
    }

    /**
     * @test
     */
    public function it_should_grab_user_from_database_by_id()
    {
        $user = User::factory()->create();

        $returnedUser = $this->repo->getUserById($user->getId());

        $this->assertEquals($user->id, $returnedUser->id);
    }

    /**
     * @test
     */
    public function it_should_grab_user_from_database_by_email()
    {
        $user = User::factory()->create();

        $returnedUser = $this->repo->getUserByEmail($user->getEmail());

        $this->assertEquals($user->id, $returnedUser->id);
    }

    /**
     * @test
     */
    public function it_should_grab_user_from_database_by_verification_token()
    {
        $user = User::factory()->unverified()->create();

        $returnedUser = $this->repo->getUserByVerificationToken($user->verify_token);

        $this->assertEquals($user->id, $returnedUser->id);
    }

    /**
     * @test
     */
    public function it_should_return_null_when_user_doesnt_exist_by_id()
    {
        $returnedUser = $this->repo->getUserById('test');

        $this->assertNull($returnedUser);
    }

    /**
     * @test
     */
    public function it_should_return_null_when_user_doesnt_exist_by_email()
    {
        $returnedUser = $this->repo->getUserByEmail('test');

        $this->assertNull($returnedUser);
    }

    /**
     * @test
     */
    public function it_should_return_null_when_user_doesnt_exist_by_verification_token()
    {
        $returnedUser = $this->repo->getUserByVerificationToken('test');

        $this->assertNull($returnedUser);
    }
}
