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
        Schema::create('pescados', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ponto_id');
            $table->foreignId('peixe_id');
            $table->integer('comprimento')->nullable(); // centimetros
            $table->integer('peso')->nullable(); // gramas
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
        Schema::dropIfExists('pescados');
    }
};
