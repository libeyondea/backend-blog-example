<?php

namespace Database\Factories;

use App\Models\ArticleCategory;
use App\Models\Article;
use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

class ArticleCategoryFactory extends Factory
{
	/**
	 * The name of the factory's corresponding model.
	 *
	 * @var string
	 */
	protected $model = ArticleCategory::class;

	/**
	 * Define the model's default state.
	 *
	 * @return array
	 */
	public function definition()
	{
		$articles_count = Article::all()->count();
		$categories_count = Category::all()->count();
		$article_categories = [];
		for ($i = 1; $i <= $articles_count; $i++) {
			for ($j = 1; $j <= $categories_count; $j++) {
				array_push($article_categories, $i . '-' . $j);
			}
		}
		$article_and_category = $this->faker->unique->randomElement($article_categories);
		$article_and_category = explode('-', $article_and_category);
		$article_id = $article_and_category[0];
		$category_id = $article_and_category[1];
		return [
			'article_id' => $article_id,
			'category_id' => $category_id
		];
	}
}
