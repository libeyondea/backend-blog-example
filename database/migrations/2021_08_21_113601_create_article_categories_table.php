<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateArticleCategoriesTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('article_categories', function (Blueprint $table) {
			$table->id();
			$table
				->foreignId('article_id')
				->constrained('articles')
				->onUpdate('cascade')
				->onDelete('cascade');
			$table
				->foreignId('category_id')
				->constrained('categories')
				->onUpdate('cascade')
				->onDelete('cascade');
			$table->unique(['article_id', 'category_id']);
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
		Schema::dropIfExists('article_categories');
	}
}
