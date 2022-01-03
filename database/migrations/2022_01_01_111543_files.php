<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Files extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create(
            'files',
            function(Blueprint $table) {
                $table->id();
                $table->enum('module', ['forum']);
                $table->foreignId('user_id')->nullable()->constrained('users')->onUpdate('cascade');
                $table->string('name');
                $table->string('path');
                $table->timestamp('created_at');
            }
        );
        Schema::create(
            'attached_files',
            function(Blueprint $table) {
                $table->id();
                $table->foreignId('post_id')->constrained('posts')->onUpdate('cascade')->onDelete('cascade');
                $table->foreignId('file_id')->constrained('files')->onUpdate('cascade')->onDelete('cascade');
            }
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('attached_files');
        Schema::dropIfExists('files');
    }
}
