<?php

namespace App\Models;

use App\Profiles\SlugGenerator;
use App\Traits\EventDispatcher;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;
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
        'birth_date' => 'datetime',
    ];

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    protected $keyType = 'string';

    /**
     * Defines one-to-many (inverse) between profile & location
     */
    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

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
        $profile->user_id = $userId;
        $profile->slug = SlugGenerator::generate($firstName . ' ' . $lastName);
        $profile->setFirstName($firstName);
        $profile->setLastName($lastName);
        $profile->setGender($gender);
        $profile->setInstitution($institution);
        $profile->setBio($bio);
        $profile->setInstruments($instruments);
        $profile->setBirthDate($birthDate);

        return $profile;
    }

    public function edit(
        ?string $firstName,
        ?string $lastName,
        ?string $gender,
        ?string $institution,
        ?string $bio,
        ?array $instruments,
        ?Carbon $birthDate
    ): self {

        $this->setFirstName($firstName);
        $this->setLastName($lastName);
        $this->setGender($gender);
        $this->setInstitution($institution);
        $this->setBio($bio);
        $this->setInstruments($instruments);
        $this->setBirthDate($birthDate);

        return $this;
    }

    private function setFirstName(?string $firstName)
    {
        if (!isset($firstName)) {
            return;
        }
        $this->first_name = $firstName;
    }

    private function setLastName(?string $lastName)
    {
        if (!isset($lastName)) {
            return;
        }
        $this->last_name = $lastName;
    }

    private function setGender(?string $gender)
    {
        if (!isset($gender)) {
            return;
        }
        $this->gender = $gender;
    }

    private function setBio(?string $bio)
    {
        if (!isset($bio)) {
            return;
        }
        $this->bio = $bio;
    }

    private function setInstitution(?string $institution)
    {
        if (!isset($institution)) {
            return;
        }
        $this->institution = $institution;
    }

    private function setInstruments(?array $instruments)
    {
        if (!isset($instruments)) {
            return;
        }
        $this->instruments = $this->instrumentsArrayToJson($instruments);
    }

    private function setBirthDate(?Carbon $birthDate)
    {
        if (!isset($birthDate)) {
            return;
        }
        $this->birth_date = $birthDate;
    }


    public function setLocation(string $countryCode, string $city)
    {
        $location = Location::where('country_code', '=', $countryCode)
            ->where('city', '=', $city)
            ->first();

        $this->location()->associate($location);
    }

    /**
     * Accessor method for the appended attribute
     *
     * @return bool
     */
    public function getIsOwnerAttribute()
    {
        /** @var User $user */
        $user = Auth::user();

        return $this->isOwnedBy($user);
    }

    /**
     * Checks if the profile is owned by a user
     *
     * @param User $user
     *
     * @return bool
     */
    public function isOwnedBy(User $user): bool
    {
        return $this->user_id === $user->id;
    }

    private function instrumentsArrayToJson(array $instruments): string
    {
        return json_encode($instruments);
    }

    public function getFirstName(): string
    {
        return $this->first_name;
    }


    public function getInstitution(): string
    {
        return $this->institution;
    }

    public function getCreatedAt(): Carbon
    {
        return $this->created_at;
    }

    public function getBirthDate(): Carbon
    {
        return $this->birth_date;
    }

    public function getProfileImageUrl(): string
    {
        return $this->profile_image_url;
    }

    public function getLocationId(): int
    {
        return $this->location_id;
    }

    public function getUserId(): string
    {
        return $this->user_id;
    }

    public function getGender(): string
    {
        return $this->gender;
    }

    public function getBio(): string
    {
        return $this->bio;
    }

    public function getInstruments(): array
    {
        return json_decode($this->instruments);
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function getLastName(): string
    {
        return $this->last_name;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getLocation(): Location
    {
        return $this->location;
    }

    public function setProfileImageUrl(string $profile_image_url): void
    {
        $this->profile_image_url = $profile_image_url;
    }
}
