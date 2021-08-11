<?php

namespace Database\Factories;

use App\Models\Tag;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class TagFactory extends Factory
{
	/**
	 * The name of the factory's corresponding model.
	 *
	 * @var string
	 */
	protected $model = Tag::class;

	/**
	 * Define the model's default state.
	 *
	 * @return array
	 */
	public function definition()
	{
		$title = Str::ucfirst($this->faker->unique()->word());
		return [
			'title' => $title,
			'slug' => Str::slug($title, '-'),
			'content' => $this->faker->sentence()
		];
	}
}
