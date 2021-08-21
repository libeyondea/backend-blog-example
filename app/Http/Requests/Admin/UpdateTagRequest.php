<?php

namespace App\Http\Requests\Admin;

use Illuminate\Support\Str;
use App\Http\Requests\CustomRequest;
use Illuminate\Validation\Rule;

class UpdateTagRequest extends CustomRequest
{
	public function rules()
	{
		return [
			'slug' => 'nullable|string',
			'title' => 'required|string|max:166',
			'content' => 'required|string|max:200'
		];
	}

	public function messages()
	{
		return [
			'slug.unique' => 'Slug already exists',
			'title.required' => 'Title is required',
			'content.required' => 'Content is required'
		];
	}

	protected function prepareForValidation()
	{
	}
}
