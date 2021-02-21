<?php

namespace Tests\Feature\Repositories;

use App\Models\PasswordResetToken;
use App\Models\User;
use App\Repositories\User\PasswordResetRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PasswordResetRepositoryTest extends TestCase
{
    use RefreshDatabase;

    protected PasswordResetRepository $repo;

    /**
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->repo = $this->app->make(PasswordResetRepository::class);
    }

    /**
     * @test
     */
    public function it_should_save_token_to_database()
    {
        $user = User::factory()->create();
        $token = PasswordResetToken::create($user->getId());

        $this->repo->save($token);

        $this->assertDatabaseHas('password_resets', [
            'user_id' => $user->getId()
        ]);
    }

    /**
     * @test
     */
    public function it_should_grab_token_from_database_by_id()
    {
        $token = PasswordResetToken::factory()->create();

        $returnedToken = $this->repo->getTokenById($token->getToken());

        $this->assertEquals($token->id, $returnedToken->id);
    }

    /**
     * @test
     */
    public function it_should_delete_token_by_id()
    {
        $token = PasswordResetToken::factory()->create();

        $this->assertDatabaseHas('password_resets', [
            'id' => $token->getToken()
        ]);

        $this->repo->deleteById($token->getToken());

        $this->assertDatabaseMissing('password_resets', [
            'id' => $token->getToken()
        ]);
    }


    /**
     * @test
     */
    public function it_should_return_null_when_token_doesnt_exist_by_id()
    {
        $returnedToken = $this->repo->getTokenById('test');

        $this->assertNull($returnedToken);
    }
}
