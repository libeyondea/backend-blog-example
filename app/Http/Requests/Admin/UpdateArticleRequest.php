<?php

namespace App\Http\Requests\Admin;

use Illuminate\Support\Str;
use App\Http\Requests\CustomRequest;

class UpdateArticleRequest extends CustomRequest
{
	public function rules()
	{
		return [
			'slug' => 'nullable|string',
			'title' => 'required|string|max:166',
			'content' => 'required|string|max:60000',
			'categories' => 'required|array|min:1|max:6',
			'image' => 'nullable|image|mimes:jpeg,jpg,png,gif|max:2048',
			'tags' => 'required|array|min:1|max:6',
			'tags.*.title' => 'required|string|max:66'
		];
	}

	public function messages()
	{
		return [
			'title.required' => 'Title is required',
			'content.required' => 'Content is required',
			'categories.required' => 'Categories is required',
			'image.image' => 'Image must be an image file',
			'image.mimes' => 'Image file must be .png .jpg .jpeg .gif',
			'image.max' => 'Maximum image size to upload is 5000kb',
			'tags.required' => 'Tags is required',
			'tags.array' => 'Tags must be an array',
			'tags.min' => 'Tags must have an item',
			'tags.max' => 'Add up to 6 tags'
		];
	}

	protected function prepareForValidation()
	{
		$this->merge([
			'tags' => json_decode($this->tags, true),
			'categories' => json_decode($this->categories, true)
		]);
	}
}
