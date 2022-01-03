<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMahoukaServerEventsTable extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create(
            'mahouka_server_events',
            function(Blueprint $table) {
                $table->id();
                $table->text('name');
                $table->date('date');
                $table->enum('type', ['release', 'announcement', 'other']);
                $table->boolean('important');
                $table->foreignId('series_id')->nullable()->constrained('mahouka_series');
            }
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::dropIfExists('mahouka_server_events');
    }
}
