<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ArticleTag;

class ArticleTagSeeder extends Seeder
{
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		ArticleTag::factory(166)->create();
	}
}
