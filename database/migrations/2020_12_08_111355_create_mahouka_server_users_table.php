<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMahoukaServerUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mahouka_server_users', function (Blueprint $table) {
            $table->id();
            $table->text('name');
            $table->text('alias')->nullable();
            $table->boolean('hidden')->default(false);
            $table->date('join_date')->nallable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mahouka_server_users');
    }
}
