<?php

namespace Tests\Feature\Resources;

use App\Http\Resources\ProfileResource;
use App\Models\Profile;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Tests\TestCase;

class ProfileResourceTest extends TestCase
{
    use RefreshDatabase;

    protected Profile $profile;

    public function setUp(): void
    {
        parent::setUp();

        $user = User::factory()->create();
        $this->be($user);
        $this->profile = Profile::factory()->create([
            'profile_image_url' => 'test-image.com/avatar'
        ]);
    }

    /**
     * @test
     */
    public function it_should_return_profile_correctly()
    {
        Storage::shouldReceive('url')
            ->once()
            ->with($this->profile->getProfileImageUrl())
            ->andReturn('https://avatar.url');

        $request = request();
        $resource = ProfileResource::collection(Collection::make([$this->profile]))
            ->toArray($request)[0];

        $resource['location'] = $resource['location']->toArray($request);

        $this->assertNotEmpty($resource);

        $this->assertEquals([
            'id' => $this->profile->getId(),
            'slug' => $this->profile->getSlug(),
            'first_name' => $this->profile->getFirstName(),
            'last_name' => $this->profile->getLastName(),
            'gender' => $this->profile->getGender(),
            'bio' => $this->profile->getBio(),
            'profile_image_url' => 'https://avatar.url',
            'location' => $this->profile->getLocation()->toArray(),
            'instruments' => $this->profile->getInstruments(),
            'institution' => $this->profile->getInstitution(),
            'birth_date' => $this->profile->getBirthDate(),
            'is_owner' => false
        ], $resource);
    }
}
