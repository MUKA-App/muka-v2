<?php

namespace Database\Factories;

use App\Library\Genders;
use App\Library\Instruments;
use App\Models\Location;
use App\Models\Profile;
use App\Models\User;
use App\Profiles\SlugGenerator;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ProfileFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Profile::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $user = User::factory()->create();
        $firstName =  $this->faker->firstName;
        $lastName = $this->faker->lastName;
        return [
            'id'=> $this->faker->uuid,
            'first_name' => $firstName,
            'last_name' => $lastName,
            'slug' => SlugGenerator::generate($firstName . ' ' . $lastName),
            'gender' => $this->faker->randomElement(Genders::GENDERS),
            'institution' => $this->faker->text(255),
            'instruments' => json_encode($this->faker->randomElements(Instruments::INSTRUMENTS, 5)),
            'birth_date' => Carbon::now(),
            'bio' => $this->faker->text(4000),
            'location_id' => Location::all()->random(1)->first()->id,
            'profile_image_url' => "profile_pictures/{$this->faker->slug}.png",
            'user_id' => $user->getId()
        ];
    }
}
