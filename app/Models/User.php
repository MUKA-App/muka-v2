<?php

namespace App\Models;

use App\Traits\EventDispatcher;
use Carbon\Carbon;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * Class User
 * @package App\Models
 * @property string $id The uuid of the model
 * @property string $email The email of the user
 * @property string $password The password hash of the user
 * @property string $verify_token the user verification token
 * @property Carbon $email_verified_at the time the user verified the email
 * @property Carbon $created_at
 */
class User extends Authenticatable
{
    use HasFactory;
    use Notifiable;
    use EventDispatcher;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
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


    public static function create(string $email, string $password): self
    {
        $user = new self();

        $user->id = Str::uuid()->toString();
        $user->email = $email;
        $user->password = Hash::make($password);
        $user->verify_token = sha1(Str::uuid()->toString());

        return $user;
    }

    /**
     * Resolves validation state of user
     */
    public function isVerified(): bool
    {
        if ($this->getVerifiedAt()) {
            return true;
        }

        return false;
    }

    public function getCreatedAt(): Carbon
    {
        return $this->created_at;
    }

    public function getVerifiedAt(): ?Carbon
    {
        return $this->email_verified_at;
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    public function verify(): void
    {
        $this->verify_token = null;
        $this->email_verified_at = Carbon::now();
    }

    public function resetPassword(string $password): void
    {
        $this->password = Hash::make($password);
    }
}
