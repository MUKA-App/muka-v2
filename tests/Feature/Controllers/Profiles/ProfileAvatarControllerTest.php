<?php

namespace Tests\Feature\Controllers\Profiles;

use App\Models\Profile;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProfileAvatarControllerTest extends TestCase
{
    use RefreshDatabase;
    use WithoutMiddleware;

    private User $user;

    private Profile $profile;

    private UploadedFile $image;

    private UploadedFile $newImage;


    public function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
        $this->image = UploadedFile::fake()->image('avatar.png');
        $this->newImage = UploadedFile::fake()->image('new-avatar.png');
        // Authenticate as created user
        $this->be($this->user);
    }

    /**
     * @test
     * Tests no avatar exists, then is stored and url persisted in DB
     */
    public function test_put_endpoint_adds_avatar()
    {
        $this->profile = Profile::factory()->create([
            'profile_image_url' => null,
            'user_id' => $this->user->getId()
        ]);

        // Assert no avatar
        $this->assertEmpty($this->profile->getProfileImageUrl());
        $this->assertImageMissing($this->image);

        // Make request with image
        $response = $this->json('put', '/api/profile/avatar', [
            'avatar' => $this->image
        ]);
        $response->assertStatus(204);

        $this->assertResponseMatchesApiSpecs($response, '/profile/avatar', 'put');

        $this->assertImageExists($this->image);
    }

    /**
     * Method asserts given image not in storage
     * @param $image
     */
    private function assertImageMissing($image): void
    {
        Storage::disk('public')
            ->assertMissing('profile_pictures/' . $image->hashName());
    }

    /**
     * Asserts DB contains new image path
     * @param $image
     */
    private function assertImageExists($image): void
    {
        // Assert new image stored
        Storage::disk('public')
            ->assertExists('/profile_pictures/' . $image->hashName());

        $this->assertDatabaseHas('profiles', [
            'id' => $this->profile->id,
            'profile_image_url' => 'profile_pictures/' . $image->hashName()
        ]);
    }

    /**
     * @test
     * Seeds the database with an image, and controller overrides path and file storage
     */
    public function test_put_endpoint_overrides_old_image()
    {
        $this->profile = Profile::factory()->create([
            'user_id' => $this->user->getId()
        ]);
        $this->manuallyStoreImage($this->image);

        $response = $this->json('put', '/api/profile/avatar', [
            'avatar' => $this->newImage
        ]);
        $response->assertStatus(204);

        $this->assertResponseMatchesApiSpecs($response, '/profile/avatar', 'put');

        // Assert first image removed
        $this->assertImageMissing($this->image);

        $this->assertImageExists($this->newImage);
    }

    /**
     * The controller should return an invalid image type error
     * @test
     */
    public function test_validation_returns_invalid_image_type_error()
    {
        $response = $this->json('put', '/api/profile/avatar', [
            'avatar' => UploadedFile::fake()->image('bob.gif')
        ]);
        $response->assertStatus(422);

        $this->assertResponseMatchesApiSpecs($response, '/profile/avatar', 'put');
    }

    /**
     * The controller should return an invalid image size error
     * @test
     */
    public function test_invalid_size_error()
    {
        $response = $this->json('put', '/api/profile/avatar', [
            'avatar' => $this->image->size(2049)
        ]);
        $response->assertStatus(422);

        $this->assertResponseMatchesApiSpecs($response, '/profile/avatar', 'put');
    }

    /**
     * Controller should return 403 if no guest profile associated with user
     * @test
     */
    public function test_controller_aborts_without_profile_configured()
    {
        $response = $this->json('put', '/api/profile/avatar', [
            'avatar' => $this->image
        ]);
        $response->assertStatus(404);

        $this->assertResponseMatchesApiSpecs($response, '/profile/avatar', 'put');
    }

    /**
     * Store the image and save to guest, without request
     * @param $image
     */
    private function manuallyStoreImage($image): void
    {
        $this->profile->profile_image_url = Storage::putFile('profile_pictures', $image);
        $this->profile->save();

        $this->assertDatabaseHas('profiles', [
            'id' => $this->profile->id,
            'profile_image_url' => 'profile_pictures/' . $image->hashName()
        ]);
    }
}
