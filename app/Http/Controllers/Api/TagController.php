<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\CustomController;
use Illuminate\Http\Request;
use App\Models\Tag;
use App\Transformers\TagTransformer;

class TagController extends CustomController
{
	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index(Request $request)
	{
		$offset = $request->get('offset', 0);
		$limit = $request->get('limit', 10);

		$categoryQuery = Tag::withCount('articles')
			->orderBy('articles_count', 'desc')
			->orderBy('created_at', 'desc');

		$categoriesCount = $categoryQuery->get()->count();
		$categories = fractal(
			$categoryQuery
				->skip($offset)
				->take($limit)
				->get(),
			new TagTransformer()
		);
		return $this->respondSuccessWithPagination($categories, $categoriesCount);
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @return \Illuminate\Http\Response
	 */
	public function store(Request $request)
	{
		//
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function show($id)
	{
		$tagQuery = Tag::where('slug', $id);
		$tag = fractal($tagQuery->firstOrFail(), new TagTransformer());
		return $this->respondSuccess($tag);
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  \Illuminate\Http\Request  $request
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function update(Request $request, $id)
	{
		//
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function destroy($id)
	{
		//
	}
}
