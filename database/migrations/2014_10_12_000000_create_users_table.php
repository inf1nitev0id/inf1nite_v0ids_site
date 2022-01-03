<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create(
            'users',
            function(Blueprint $table) {
                $table->id();
                $table->string('name')->unique();
                $table->string('email')->unique();
                $table->timestamp('email_verified_at')->nullable();
                $table->string('password');
                $table->foreignId('invite_id')->nullable();
                $table->enum('role', ['user', 'moderator', 'admin'])->default('user');
                $table->rememberToken();
                $table->timestamps();
                $table->foreign('invite_id')->references('id')->on('invites');
            }
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('users');
    }
}
