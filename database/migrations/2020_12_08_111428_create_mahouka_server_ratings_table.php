<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMahoukaServerRatingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mahouka_server_ratings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('mahouka_server_users');
            $table->integer('rate');
            $table->date('date');
            $table->boolean('time');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mahouka_server_ratings');
    }
}
