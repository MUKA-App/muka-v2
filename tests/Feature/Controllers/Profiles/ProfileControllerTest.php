<?php

declare(strict_types=1);

namespace Tests\Feature\Controllers\Profiles;

use App\Library\Genders;
use App\Models\Location;
use App\Models\Profile;
use App\Models\User;
use App\Repositories\Profiles\ProfileRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Str;
use Tests\TestCase;

class ProfileControllerTest extends TestCase
{
    use RefreshDatabase;
    use WithoutMiddleware;

    private User $user;

    private Profile $profile;

    private array $requestData;

    private array $updateData;

    private ProfileRepositoryInterface $repository;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->be($this->user);

        $this->requestData = $this->requestData();
        $this->updateData = $this->updateData();
        $this->repository = App::make(ProfileRepositoryInterface::class);
    }

    /**
     * Returns array of valid request data
     */
    private function requestData(): array
    {
        return [
            'first_name' => 'Rick',
            'last_name' => 'Sanchez',
            'bio' => 'My awesome bio',
            'gender' => Genders::GENDERS[0],
            'country' => 'TE',
            'city' => 'Test City',
            'instruments' => ['Piano', 'Violin'],
            'institution' => 'Awesome Music School',
            'birth_date' => Carbon::create(2000, 01, 01),
        ];
    }


    /**
     * Get data to test profile update
     * @return array
     */
    private function updateData()
    {
        return [
            'first_name' => 'Morty',
            'last_name' => 'Smith',
            'bio' => 'My changed bio',
            'gender' => Genders::GENDERS[1],
            'country' => 'FE',
            'city' => 'Fake City',
            'instruments' => ['Bagpipes'],
            'institution' => 'Not that good school',
            'birth_date' => Carbon::create(1999, 01, 01),
        ];
    }

    /**
     * @test
     */
    public function it_should_get_users_profile()
    {
        $this->createProfileForUser($this->user);

        $response = $this->json('GET', '/api/profile')
            ->assertStatus(200);

        $this->assertResponseMatchesApiSpecs($response, '/profile', 'get');
    }

    /**
     * @test
     */
    public function it_should_return_404_when_the_user_profile_does_not_exist()
    {
        $response = $this->json('GET', '/api/profile')
            ->assertStatus(404);

        $this->assertResponseMatchesApiSpecs($response, '/profile', 'get');

        $data = $response->decodeResponseJson();

        $this->assertEquals("This profile does not exist", $data['message']);
    }

    /**
     * @test
     */
    public function it_should_get_profile_for_non_owner()
    {
        $user2 = User::factory()->create();
        $profile = $this->createProfileForUser($user2);

        $response = $this->json('GET', '/api/profile/' . $profile->getSlug())
            ->assertStatus(200);

        $this->assertResponseMatchesApiSpecs($response, '/profile/{slug}', 'get');
    }


    /**
     * @test
     */
    public function it_should_return_404_when_the_profile_does_not_exist()
    {
        $response = $this->json('GET', '/api/profile/' . 'testyslug')
            ->assertStatus(404);

        $data = $response->decodeResponseJson();

        $this->assertEquals("This profile does not exist", $data['message']);
        $this->assertResponseMatchesApiSpecs($response, '/profile/{slug}', 'get');
    }


    /**
     * @test
     */
    public function it_should_create_profile()
    {
        $response = $this->json('post', '/api/profile', $this->requestData)
            ->assertSuccessful();

        $this->assertResponseMatchesApiSpecs($response, '/profile', 'post');

        $data = $response->decodeResponseJson()['data'];

        $location = Location::where('country_code', 'TE')->first();

        $this->assertDatabaseHas('profiles', [
            'id' => $data['id'],
            'bio' => $data['bio'],
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'location_id' => $location->id
        ]);
    }


    /**
     * @test
     */
    public function it_should_not_create_profile_when_user_has_one()
    {
        $this->createProfileForUser($this->user);
        $response = $this->json('post', '/api/profile', $this->requestData)
            ->assertStatus(409);

        $this->assertResponseMatchesApiSpecs($response, '/profile', 'post');
    }


    /**
     * @test
     */
    public function it_should_update_profile()
    {
        $this->createProfileForUser($this->user);

        $response = $this->json('patch', '/api/profile', $this->updateData)
            ->assertSuccessful();

        $this->assertResponseMatchesApiSpecs($response, '/profile', 'patch');

        $data = $response->decodeResponseJson()['data'];

        $location = Location::where('country_code', 'FE')->first();

        $this->assertDatabaseHas('profiles', [
            'id' => $data['id'],
            'bio' => $data['bio'],
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'location_id' => $location->id
        ]);
    }

    /**
     * @test
     */
    public function it_should_not_update_profile_when_user_does_not_have_one()
    {
        $response = $this->json('patch', '/api/profile', $this->updateData)
            ->assertStatus(404);

        $this->assertResponseMatchesApiSpecs($response, '/profile', 'patch');
    }

    /**
     * @test
     * @dataProvider getIncompleteCreateData
     */
    public function it_should_return_422_when_data_is_incomplete_with_create(array $faultyData)
    {
        $response = $this->json('post', '/api/profile', $faultyData)
            ->assertStatus(422);

        $this->assertResponseMatchesApiSpecs($response, '/profile', 'post');
    }

    /**
     * @test
     * @dataProvider getIncompleteUpdateData
     */
    public function it_should_update_profile_when_not_all_fields_are_present(array $incompleteData)
    {
        $this->createProfileForUser($this->user);
        $response = $this->json('patch', '/api/profile', $incompleteData)
            ->assertSuccessful();

        $this->assertResponseMatchesApiSpecs($response, '/profile', 'patch');
    }


    /**
     * @test
     * @dataProvider getFaultyData
     */
    public function it_should_return_422_when_data_is_incorrect_with_create(array $faultyData)
    {
        $response = $this->json('post', '/api/profile', $faultyData)
            ->assertStatus(422);

        $this->assertResponseMatchesApiSpecs($response, '/profile', 'post');
    }

    /**
     * @test
     * @dataProvider getFaultyData
     */
    public function it_should_return_422_when_data_is_incorrect_with_update(array $faultyData)
    {
        $response = $this->json('patch', '/api/profile', $faultyData)
            ->assertStatus(422);

        $this->assertResponseMatchesApiSpecs($response, '/profile', 'patch');
    }


    /**
     * Returns array of invalid data for testing invalid request
     */
    public function getIncompleteCreateData(): array
    {
        return [
            [[
                'last_name' => 'Sanchez',
                'bio' => 'My awesome bio',
                'gender' => Genders::GENDERS[0],
                'country' => 'TE',
                'city' => 'Test City',
                'instruments' => ['Piano', 'Violin'],
                'institution' => 'Awesome Music School',
                'birth_date' => Carbon::create(2000, 01, 01),
            ]],
            [[
                'first_name' => 'Rick',
                'bio' => 'My awesome bio',
                'gender' => Genders::GENDERS[0],
                'country' => 'TE',
                'city' => 'Test City',
                'instruments' => ['Piano', 'Violin'],
                'institution' => 'Awesome Music School',
                'birth_date' => Carbon::create(2000, 01, 01),
            ]],
            [[
                'first_name' => 'Rick',
                'last_name' => 'Sanchez',
                'gender' => Genders::GENDERS[0],
                'country' => 'TE',
                'city' => 'Test City',
                'instruments' => ['Piano', 'Violin'],
                'institution' => 'Awesome Music School',
                'birth_date' => Carbon::create(2000, 01, 01),
            ]],
            [[
                'first_name' => 'Rick',
                'last_name' => 'Sanchez',
                'bio' => 'My awesome bio',
                'country' => 'TE',
                'city' => 'Test City',
                'instruments' => ['Piano', 'Violin'],
                'institution' => 'Awesome Music School',
                'birth_date' => Carbon::create(2000, 01, 01),
            ]],
            [[
                'first_name' => 'Rick',
                'last_name' => 'Sanchez',
                'bio' => 'My awesome bio',
                'gender' => Genders::GENDERS[0],
                'city' => 'Test City',
                'instruments' => ['Piano', 'Violin'],
                'institution' => 'Awesome Music School',
                'birth_date' => Carbon::create(2000, 01, 01),
            ]],
            [[
                'first_name' => 'Rick',
                'last_name' => 'Sanchez',
                'bio' => 'My awesome bio',
                'gender' => Genders::GENDERS[0],
                'country' => 'TE',
                'instruments' => ['Piano', 'Violin'],
                'institution' => 'Awesome Music School',
                'birth_date' => Carbon::create(2000, 01, 01),
            ]],
            [[
                'first_name' => 'Rick',
                'last_name' => 'Sanchez',
                'bio' => 'My awesome bio',
                'gender' => Genders::GENDERS[0],
                'country' => 'TE',
                'city' => 'Test City',
                'institution' => 'Awesome Music School',
                'birth_date' => Carbon::create(2000, 01, 01),
            ]],
            [[
                'first_name' => 'Rick',
                'last_name' => 'Sanchez',
                'bio' => 'My awesome bio',
                'gender' => Genders::GENDERS[0],
                'country' => 'TE',
                'city' => 'Test City',
                'instruments' => ['Piano', 'Violin'],
                'birth_date' => Carbon::create(2000, 01, 01),
            ]],
            [[
                'first_name' => 'Rick',
                'last_name' => 'Sanchez',
                'bio' => 'My awesome bio',
                'gender' => Genders::GENDERS[0],
                'country' => 'TE',
                'city' => 'Test City',
                'instruments' => ['Piano', 'Violin'],
                'institution' => 'Awesome Music School',
            ]]
        ];
    }

    /**
     * Returns array of invalid data for testing invalid request
     */
    public function getIncompleteUpdateData(): array
    {
        return [
            [[
                'last_name' => 'Sanchez',
                'bio' => 'My awesome bio',
                'gender' => Genders::GENDERS[0],
                'country' => 'TE',
                'city' => 'Test City',
                'instruments' => ['Piano', 'Violin'],
                'institution' => 'Awesome Music School',
                'birth_date' => Carbon::create(2000, 01, 01),
            ]],
            [[
                'first_name' => 'Rick',
                'bio' => 'My awesome bio',
                'gender' => Genders::GENDERS[0],
                'country' => 'TE',
                'city' => 'Test City',
                'instruments' => ['Piano', 'Violin'],
                'institution' => 'Awesome Music School',
                'birth_date' => Carbon::create(2000, 01, 01),
            ]],
            [[
                'first_name' => 'Rick',
                'last_name' => 'Sanchez',
                'gender' => Genders::GENDERS[0],
                'country' => 'TE',
                'city' => 'Test City',
                'instruments' => ['Piano', 'Violin'],
                'institution' => 'Awesome Music School',
                'birth_date' => Carbon::create(2000, 01, 01),
            ]],
            [[
                'first_name' => 'Rick',
                'last_name' => 'Sanchez',
                'bio' => 'My awesome bio',
                'country' => 'TE',
                'city' => 'Test City',
                'instruments' => ['Piano', 'Violin'],
                'institution' => 'Awesome Music School',
                'birth_date' => Carbon::create(2000, 01, 01),
            ]],
            [[
                'first_name' => 'Rick',
                'last_name' => 'Sanchez',
                'bio' => 'My awesome bio',
                'gender' => Genders::GENDERS[0],
                'country' => 'TE',
                'city' => 'Test City',
                'institution' => 'Awesome Music School',
                'birth_date' => Carbon::create(2000, 01, 01),
            ]],
            [[
                'first_name' => 'Rick',
                'last_name' => 'Sanchez',
                'bio' => 'My awesome bio',
                'gender' => Genders::GENDERS[0],
                'country' => 'TE',
                'city' => 'Test City',
                'instruments' => ['Piano', 'Violin'],
                'birth_date' => Carbon::create(2000, 01, 01),
            ]],
            [[
                'first_name' => 'Rick',
                'last_name' => 'Sanchez',
                'bio' => 'My awesome bio',
                'gender' => Genders::GENDERS[0],
                'country' => 'TE',
                'city' => 'Test City',
                'instruments' => ['Piano', 'Violin'],
                'institution' => 'Awesome Music School',
            ]]
        ];
    }

    public function getFaultyData(): array
    {
        return [
            [[
                'first_name' => 1,
                'last_name' => 'Sanchez',
                'bio' => 'My awesome bio',
                'gender' => Genders::GENDERS[0],
                'country' => 'TE',
                'city' => 'Test City',
                'instruments' => ['Piano', 'Violin'],
                'institution' => 'Awesome Music School',
                'birth_date' => Carbon::create(2000, 01, 01),
            ]],
            [[
                'first_name' => 'Rick',
                'last_name' => 2,
                'bio' => 'My awesome bio',
                'gender' => Genders::GENDERS[0],
                'country' => 'TE',
                'city' => 'Test City',
                'instruments' => ['Piano', 'Violin'],
                'institution' => 'Awesome Music School',
                'birth_date' => Carbon::create(2000, 01, 01),
            ]],
            [[
                'first_name' => 'Rick',
                'last_name' => 'Sanchez',
                'bio' => 3,
                'gender' => Genders::GENDERS[0],
                'country' => 'TE',
                'city' => 'Test City',
                'instruments' => ['Piano', 'Violin'],
                'institution' => 'Awesome Music School',
                'birth_date' => Carbon::create(2000, 01, 01),
            ]],
            [[
                'first_name' => 'Rick',
                'last_name' => 'Sanchez',
                'bio' => 'My awesome bio',
                'gender' => 'not in enum',
                'country' => 'TE',
                'city' => 'Test City',
                'instruments' => ['Piano', 'Violin'],
                'institution' => 'Awesome Music School',
                'birth_date' => Carbon::create(2000, 01, 01),
            ]],
            [[
                'first_name' => 'Rick',
                'last_name' => 'Sanchez',
                'bio' => 'My awesome bio',
                'gender' => Genders::GENDERS[0],
                'country' => 'NA',
                'city' => 'Test City',
                'instruments' => ['Piano', 'Violin'],
                'institution' => 'Awesome Music School',
                'birth_date' => Carbon::create(2000, 01, 01),
            ]],
            [[
                'first_name' => 'Rick',
                'last_name' => 'Sanchez',
                'bio' => 'My awesome bio',
                'gender' => Genders::GENDERS[0],
                'country' => 'TE',
                'city' => 'Not A City',
                'instruments' => ['Piano', 'Violin'],
                'institution' => 'Awesome Music School',
                'birth_date' => Carbon::create(2000, 01, 01),
            ]],
            [[
                'first_name' => 'Rick',
                'last_name' => 'Sanchez',
                'bio' => 'My awesome bio',
                'gender' => Genders::GENDERS[0],
                'country' => 'TE',
                'city' => 'Test City',
                'instruments' => 'notAnArray',
                'institution' => 'Awesome Music School',
                'birth_date' => Carbon::create(2000, 01, 01),
            ]],
            [[
                'first_name' => 'Rick',
                'last_name' => 'Sanchez',
                'bio' => 'My awesome bio',
                'gender' => Genders::GENDERS[0],
                'country' => 'TE',
                'city' => 'Test City',
                'instruments' => ['Piano', 'Violin'],
                'institution' => 1,
                'birth_date' => Carbon::create(2000, 01, 01),
            ]],
            [[
                'first_name' => 'Rick',
                'last_name' => 'Sanchez',
                'bio' => 'My awesome bio',
                'gender' => Genders::GENDERS[0],
                'country' => 'TE',
                'city' => 'Test City',
                'instruments' => ['Piano', 'Violin'],
                'institution' => 'Awesome Music School',
                'birth_date' => 'string instead of date',
            ]]
        ];
    }

    private function createProfileForUser(User $user): Profile
    {
        return Profile::factory()->create([
            'user_id' => $user->getId()
        ]);
    }
}
