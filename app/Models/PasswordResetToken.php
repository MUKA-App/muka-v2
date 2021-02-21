<?php

namespace App\Models;

use App\Traits\EventDispatcher;
use Carbon\Carbon;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * Class PasswordResetToken
 * @package App\Models
 * @property string $id The password reset token
 * @property string $user_id The user it belongs to
 */
class PasswordResetToken extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'user_id',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'id' => 'string',
    ];

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    protected $keyType = 'string';

    protected $table = 'password_resets';

    public static function create(string $userId): self
    {
        $token = new self();

        $token->id = Str::uuid()->toString();
        $token->user_id = $userId;

        return $token;
    }

    public function getUserId(): string
    {
        return $this->user_id;
    }

    public function getToken(): string
    {
        return $this->id;
    }
}
