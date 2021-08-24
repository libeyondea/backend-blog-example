<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCommentsTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('comments', function (Blueprint $table) {
			$table->id();
			$table
				->foreignId('article_id')
				->constrained('articles')
				->onUpdate('cascade')
				->onDelete('cascade');
			$table
				->foreignId('user_id')

				->constrained('users')
				->onUpdate('cascade')
				->onDelete('cascade');
			$table
				->foreignId('parent_id')
				->nullable()
				->constrained('comments')
				->onUpdate('cascade')
				->onDelete('cascade');
			$table->text('content');
			$table->tinyInteger('approved')->default('1');
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
		Schema::dropIfExists('comments');
	}
}
