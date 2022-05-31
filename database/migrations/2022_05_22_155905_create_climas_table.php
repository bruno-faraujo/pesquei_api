<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('climas', function (Blueprint $table) {
            $table->id();
            $table->string('cidade');
            $table->string('estado');
            $table->integer('dt');
            $table->integer('weather_id')->nullable();
            $table->string('weather_main')->nullable();
            $table->string('weather_description')->nullable();
            $table->string('weather_icon')->nullable();
            $table->float('main_temp')->nullable();
            $table->float('main_feels_like')->nullable();
            $table->float('main_pressure')->nullable();
            $table->string('main_pressure_tendency')->nullable();
            $table->float('main_humidity')->nullable();
            $table->integer('visibility')->nullable();
            $table->float('wind_speed')->nullable();
            $table->integer('wind_deg')->nullable();
            $table->float('wind_gust')->nullable();
            $table->float('clouds_all')->nullable();
            $table->float('rain_1h')->nullable();
            $table->float('rain_3h')->nullable();
            $table->float('snow_1h')->nullable();
            $table->float('snow_3h')->nullable();
            $table->integer('sys_sunrise')->nullable();
            $table->integer('sys_sunset')->nullable();





            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('climas');
    }
};
