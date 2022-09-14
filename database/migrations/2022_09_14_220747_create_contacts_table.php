<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateContactsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contacts', function (Blueprint $table) {
            $table->id();
            $table->string('content');
            $table->string('link')->nullable(true)->default(null);
            $table->string('type');
            $table->integer('order')->default(0);

            $table->index('order');
        });

        DB::table('contacts')->insert([
            [
                'content' => 'inf1nitev0id@yandex.ru',
                'link'    => 'mailto:inf1nitev0id@yandex.ru',
                'type'    => 'email',
                'order'   => 1,
            ],
            [
                'content' => 'inf1nite_v0id#5075',
                'link'    => null,
                'type'    => 'discord',
                'order'   => 0,
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('contacts');
    }
}
