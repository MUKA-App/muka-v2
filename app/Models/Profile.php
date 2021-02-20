<?php

namespace App\Models;

use App\Profiles\SlugGenerator;
use App\Traits\EventDispatcher;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * Class User
 * @package App\Models
 * @property string $id The uuid of the model
 * @property string $first_name The first name of the user profile
 * @property string $last_name TThe last name of the user profile
 * @property string $slug the unique slug of the profile
 * @property string $institution The institution of the user
 * @property string $instruments The instruments represented as a json string
 * @property string $bio The bio of the user
 * @property string $gender The gender of the user
 * @property string $user_id The user id of the user
 * @property int $location_id The id of the location of the user
 * @property string $profile_image_url The image url of the profile
 * @property Carbon $birth_date the date the user was born
 * @property Carbon $created_at
 */
class Profile extends Model
{
    use HasFactory;
    use EventDispatcher;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'first_name',
        'last_name',
        'slug',
        'gender',
        'institution',
        'instruments',
        'birth_date',
        'bio',
        'location_id',
        'profile_image_url',
        'user_id',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'string',
        'email_verified_at' => 'datetime',
    ];

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    protected $keyType = 'string';


    public static function create(
        string $firstName,
        string $lastName,
        string $gender,
        string $institution,
        string $bio,
        string $userId,
        array $instruments,
        Carbon $birthDate
    ): self {
        $profile = new self();

        $profile->id = Str::uuid()->toString();
        $profile->first_name = $firstName;
        $profile->last_name = $lastName;
        $profile->gender = $gender;
        $profile->institution = $institution;
        $profile->user_id = $userId;
        $profile->bio = $bio;
        $profile->slug = SlugGenerator::generate($firstName . ' ' . $lastName);
        $profile->instruments = $profile->instrumentsArrayToJson($instruments);
        $profile->birth_date = $birthDate;

        return $profile;
    }

    private function instrumentsArrayToJson(array $instruments): string
    {
        return json_encode($instruments);
    }
}
