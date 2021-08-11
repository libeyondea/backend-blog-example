<?php

namespace App\Http\Requests\Admin;

use Illuminate\Support\Str;
use App\Http\Requests\CustomRequest;

class UpdateArticleRequest extends CustomRequest
{
	public function rules()
	{
		return [
			'slug' => 'required|string|unique:articles',
			'title' => 'required|string|max:166',
			'content' => 'required|string|max:60000',
			'category' => 'required|numeric',
			'image' => 'nullable|image|mimes:jpeg,jpg,png,gif|max:2048',
			'tags' => 'required|array|min:1|max:6',
			'tags.*.title' => 'required|string|max:66',
			'pinned' => 'required|boolean',
			'published' => 'required|boolean'
		];
	}

	public function messages()
	{
		return [
			'slug.unique' => 'Slug already exists',
			'title.required' => 'Title is required',
			'content.required' => 'Content is required',
			'category.required' => 'Category is required',
			'image.image' => 'Image must be an image file',
			'image.mimes' => 'Image file must be .png .jpg .jpeg .gif',
			'image.max' => 'Maximum image size to upload is 5000kb',
			'tags.required' => 'Tag is required',
			'tags.array' => 'Tag must be an array',
			'tags.min' => 'Tag must have an item',
			'tags.max' => 'Add up to 6 tags'
		];
	}

	protected function prepareForValidation()
	{
		$this->merge([
			'tags' => json_decode($this->tags, true),
			'slug' => Str::slug($this->title, '-') . '-' . Str::lower(Str::random(4)),
			'pinned' => filter_var($this->pinned, FILTER_VALIDATE_BOOLEAN),
			'published' => filter_var($this->published, FILTER_VALIDATE_BOOLEAN)
		]);
	}
}
