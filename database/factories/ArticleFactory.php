<?php

namespace Database\Factories;

use App\Models\Article;
use App\Models\User;
use App\Models\Category;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\Factory;

class ArticleFactory extends Factory
{
	/**
	 * The name of the factory's corresponding model.
	 *
	 * @var string
	 */
	protected $model = Article::class;

	/**
	 * Define the model's default state.
	 *
	 * @return array
	 */
	public function definition()
	{
		$title = Str::remove('.', $this->faker->sentence());
		return [
			'user_id' => $this->faker->randomElement(User::pluck('id')),
			'title' => $title,
			'slug' => Str::slug($title, '-') . '-' . Str::lower(Str::random(4)),
			'excerpt' => $this->faker->paragraph(),
			'image' => '6666666666.png',
			'content' => $this->faker->text(666),
			'article_status' => 'publish',
			'comment_status' => 'open'
		];
	}
}
