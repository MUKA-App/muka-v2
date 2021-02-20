<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProfilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('profiles', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('slug')->unique();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('gender');
            $table->string('institution');
            $table->json('instruments');
            $table->string('bio', 4000);
            $table->bigInteger('location_id')->unsigned();
            $table->uuid('user_id');
            $table->string('profile_image_url', 2000);
            $table->timestamp('birth_date');
            $table->timestamps();


        });

        Schema::table('profiles', function($table) {
            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');

            $table->foreign('location_id')
                ->references('id')
                ->on('locations');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('profiles');
    }
}
