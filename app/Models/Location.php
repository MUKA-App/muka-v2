<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class Location
 * @package App\Models
 * @property string $city
 * @property string $country_code
 * @property string $country
 */
class Location extends Model
{
    /**
     * @var array
     */
    protected $fillable = [
        'country',
        'country_code',
        'city'
    ];

    protected $hidden = [
        'id',
        'created_at',
        'updated_at'
    ];


    public function getCity(): string
    {
        return $this->city;
    }

    public function getCountryCode(): string
    {
        return $this->country_code;
    }

    public function getCountry(): string
    {
        return $this->country;
    }
}
