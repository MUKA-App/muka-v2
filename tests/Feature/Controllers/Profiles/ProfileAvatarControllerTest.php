<?php

namespace Tests\Feature\Controllers\Profiles;

use App\Models\Profile;
use App\Models\User;
use App\Repositories\ProfileRepositoryInterface;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use Tests\Utils\Traits\Profiles\ProfilesTrait;

class ProfileAvatarControllerTest extends TestCase
{
    use RefreshDatabase;
    use WithoutMiddleware;

    private User $user;

    private GuestProfile $guest;

    private UploadedFile $image;

    private UploadedFile $newImage;

    private ProfileRepositoryInterface $repository;

    public function setUp(): void
    {
        parent::setUp();
        $this->user     = factory(User::class)->create();
        $this->image    = UploadedFile::fake()->image('avatar.png');
        $this->newImage = UploadedFile::fake()->image('new-avatar.png');
        // Authenticate as created user
        $this->be($this->user);
        $this->repository = App::make(ProfileRepositoryInterface::class);
    }

    /**
     * @test
     * Tests no avatar exists, then is stored and url persisted in DB
     */
    public function test_put_endpoint_adds_avatar()
    {
        $this->guest = $this->createGuestForUser($this->user, $this->repository);
        $this->guest->avatar_url = null;
        $this->repository->save($this->guest);

        // Assert no avatar
        $this->assertEmpty($this->guest->avatar_url);
        $this->assertImageMissing($this->image);

        // Make request with image
        $response = $this->json('put', '/api/profiles/guests/avatar', [
            'avatar' => $this->image
        ]);
        $response->assertStatus(204);

        $this->assertImageExists($this->image);
    }

    /**
     * Method asserts given image not in storage
     * @param $image
     */
    private function assertImageMissing($image): void
    {
        Storage::disk('public-test')
            ->assertMissing('guest_avatars/' . $image->hashName());
    }

    /**
     * Asserts DB contains new image path
     * @param $image
     */
    private function assertImageExists($image): void
    {
        // Assert new image stored
        Storage::disk('public-test')
            ->assertExists('guest_avatars/' . $image->hashName());

        $this->assertDatabaseHas('guest_profiles', [
            'id'         => $this->guest->id,
            'avatar_url' => 'guest_avatars/' . $image->hashName()
        ]);
    }

    /**
     * @test
     * Seeds the database with an image, and controller overrides path and file storage
     */
    public function test_put_endpoint_overrides_old_image()
    {
        $this->guest = $this->createGuestForUser($this->user, $this->repository);
        $this->manuallyStoreImage($this->image);

        $response = $this->json('put', '/api/profiles/guests/avatar', [
            'avatar' => $this->newImage
        ]);
        $response->assertStatus(204);

        // Assert first image removed
        $this->assertImageMissing($this->image);

        $this->assertImageExists($this->newImage);
    }

    /**
     * The controller should return an invalid image type error
     * @test
     */
    public function test_database_returns_invalid_image_type_error()
    {
        $this->guest = $this->createGuestForUser($this->user, $this->repository);
        $response = $this->json('put', '/api/profiles/guests/avatar', [
            'avatar' => UploadedFile::fake()->image('bob.gif')
        ]);
        $response->assertStatus(422);
    }

    /**
     * The controller should return an invalid image size error
     * @test
     */
    public function test_invalid_size_error()
    {
        $this->guest = $this->createGuestForUser($this->user, $this->repository);
        $response = $this->json('put', '/api/profiles/guests/avatar', [
            'avatar' => $this->image->size(2049)
        ]);
        $response->assertStatus(422);
    }

    /**
     * Controller should return 403 if no guest profile associated with user
     * @test
     */
    public function test_controller_aborts_without_profile_configured()
    {
        $response = $this->json('put', '/api/profiles/guests/avatar', [
            'avatar' => $this->image
        ]);
        $response->assertStatus(404);
    }

    /**
     * Store the image and save to guest, without request
     * @param $image
     */
    private function manuallyStoreImage($image): void
    {
        $this->guest->avatar_url = Storage::putFile('guest_avatars', $image);
        $this->guest->save();

        $this->assertDatabaseHas('guest_profiles', [
            'id' => $this->guest->id,
            'avatar_url' => 'guest_avatars/' . $image->hashName()
        ]);
    }
}
