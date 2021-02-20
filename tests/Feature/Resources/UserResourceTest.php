<?php

namespace Tests\Feature\Resources;

use App\Http\Resources\ProfileResource;
use App\Http\Resources\UserResource;
use App\Models\Profile;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Tests\TestCase;

class UserResourceTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    public function setUp(): void
    {
        parent::setUp();

        $this->user =User::factory()->create();
        $this->be($this->user);
    }

    /**
     * @test
     */
    public function it_should_return_user_correctly()
    {
        $request = request();
        $resource = UserResource::collection(Collection::make([$this->user]))
            ->toArray($request)[0];

        $this->assertNotEmpty($resource);

        $this->assertEquals([
            'id' => $this->user->getId(),
            'email' => $this->user->getEmail(),
        ], $resource);
    }
}
