<?php

namespace Tests\Feature\Repositories;

use App\Models\Profile;
use App\Models\User;
use App\Repositories\Profiles\ProfileRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProfileRepositoryTest extends TestCase
{
    use RefreshDatabase;

    protected ProfileRepository $repo;

    /**
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->repo = $this->app->make(ProfileRepository::class);
    }

    /**
     * @test
     */
    public function it_should_save_profile_to_database()
    {
        $profile = Profile::factory()->make();

        $this->repo->save($profile);

        $this->assertDatabaseHas('profiles', [
            'id' => $profile->getId()
        ]);
    }

    /**
     * @test
     */
    public function it_should_grab_profile_from_database_by_id()
    {
        $profile = Profile::factory()->create();

        $returnedProfile = $this->repo->getProfileById($profile->getId());

        $this->assertEquals($profile->id, $returnedProfile->id);
    }

    /**
     * @test
     */
    public function it_should_grab_profile_from_database_by_slug()
    {
        $profile = Profile::factory()->create();

        $returnedProfile = $this->repo->getProfileBySlug($profile->getSlug());

        $this->assertEquals($profile->id, $returnedProfile->id);
    }

    /**
     * @test
     */
    public function it_should_grab_profile_from_database_by_user_id()
    {
        $user = User::factory()->create();
        $profile = Profile::factory()->create([
            'user_id' => $user->getId()
        ]);

        $returnedProfile = $this->repo->getProfileByUserId($user->getId());

        $this->assertEquals($profile->id, $returnedProfile->id);
    }

    /**
     * @test
     */
    public function it_should_return_null_when_profile_doesnt_exist_by_id()
    {
        $returnedProfile = $this->repo->getProfileById('test');

        $this->assertNull($returnedProfile);
    }

    /**
     * @test
     */
    public function it_should_return_null_when_profile_doesnt_exist_by_slug()
    {
        $returnedProfile = $this->repo->getProfileBySlug('test');

        $this->assertNull($returnedProfile);
    }

    /**
     * @test
     */
    public function it_should_return_null_when_profile_doesnt_exist_by_user_id()
    {
        $returnedProfile = $this->repo->getProfileByUserId('test');

        $this->assertNull($returnedProfile);
    }
}
