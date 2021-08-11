<?php

namespace Database\Factories;

use App\Models\ArticleTag;
use App\Models\Article;
use App\Models\Tag;
use Illuminate\Database\Eloquent\Factories\Factory;

class ArticleTagFactory extends Factory
{
	/**
	 * The name of the factory's corresponding model.
	 *
	 * @var string
	 */
	protected $model = ArticleTag::class;

	/**
	 * Define the model's default state.
	 *
	 * @return array
	 */
	public function definition()
	{
		$articles_count = Article::all()->count();
		$tags_count = Tag::all()->count();
		$article_tags = [];
		for ($i = 1; $i <= $articles_count; $i++) {
			for ($j = 1; $j <= $tags_count; $j++) {
				array_push($article_tags, $i . '-' . $j);
			}
		}
		$article_and_tag = $this->faker->unique->randomElement($article_tags);
		$article_and_tag = explode('-', $article_and_tag);
		$article_id = $article_and_tag[0];
		$tag_id = $article_and_tag[1];
		return [
			'article_id' => $article_id,
			'tag_id' => $tag_id
		];
	}
}
