<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use MenaraSolutions\Geographer\Collections\MemberCollection;
use MenaraSolutions\Geographer\Earth;

class CreateLocationsTable extends Migration
{

    protected array $rows = [];

    protected Earth $earth;

    protected MemberCollection $countries;

    public function __construct()
    {
        $this->earth = new Earth();
        $this->countries = $this->earth->getCountries();
    }


    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('locations', function (Blueprint $table) {
            $table->id();
            $table->string('country');
            $table->string('country_code', 2);
            $table->string('city');
            $table->timestamps();
        });

        if (app()->environment() !== 'testing') {
            DB::table('locations')->insert($this->createRows());
            return;
        }

        // If testing env., mini migration with test row
        DB::table('locations')->insert([
            [
                'country' => 'Test Country',
                'country_code' => 'TE',
                'city' => 'Test City'
            ],
            [
                'country' => 'Fake Country',
                'country_code' => 'FE',
                'city' => 'Fake City'
            ]
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('locations');
    }


    private function createRows()
    {
        foreach ($this->earth->getCountries() as $country) {
            $this->citiesFromStates($country);
        }
        return $this->rows;
    }

    /**
     * @param $country
     */
    private function citiesFromStates($country)
    {
        $states = $country->getStates();
        foreach ($states as $state) {
            $this->cities($state, $country);
        }
    }

    /**
     * Create row of city & country
     * @param $state
     * @param $country
     */
    private function cities($state, $country)
    {
        $cities = $state->getCities()->toArray();
        foreach ($cities as $city) {
            array_push($this->rows, [
                'country' => $country->name,
                'country_code' => $country->getCode(),
                'city' => $city['name']
            ]);
        }
    }
}
