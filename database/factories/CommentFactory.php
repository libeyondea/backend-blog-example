<?php

namespace Database\Factories;

use App\Models\Comment;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;
use App\Models\Article;

class CommentFactory extends Factory
{
	/**
	 * The name of the factory's corresponding model.
	 *
	 * @var string
	 */
	protected $model = Comment::class;

	/**
	 * Define the model's default state.
	 *
	 * @return array
	 */
	public function definition()
	{
		return [
			'user_id' => $this->faker->randomElement(User::pluck('id')),
			'article_id' => $this->faker->randomElement(Article::pluck('id')),
			'parent_id' => null,
			'content' => $this->faker->text(222)
		];
	}
}
