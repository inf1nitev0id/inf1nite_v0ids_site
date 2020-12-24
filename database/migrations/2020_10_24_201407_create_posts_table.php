<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePostsTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('posts', function (Blueprint $table) {
			$table->id();
			$table->string('name')->nullable();
			$table->text('text')->nullable();
			$table->foreignId('parent_id')->nullable()->constrained('posts')->onUpdate('cascade')->onDelete('cascade');
			$table->enum('type', ['catalog', 'post', 'comment']);
			$table->foreignId('user_id')->constrained('users')->onUpdate('cascade')->onDelete('restrict');
			$table->boolean('deleted')->default(false);
			$table->boolean('moderator_only')->default(false);
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
		Schema::dropIfExists('posts');
	}
}
