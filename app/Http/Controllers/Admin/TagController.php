<?php

namespace App\Http\Controllers\Admin;

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
		$sortDirection = $request->get('sort_direction', 'desc');

		$tagQuery = new Tag();

		if ($request->has('sort_by')) {
			if ($request->sort_by === 'total_articles') {
				$tagQuery = $tagQuery->withCount('articles')->orderBy('articles_count', $sortDirection);
			} else {
				$tagQuery = $tagQuery->orderBy($request->sort_by, $sortDirection);
			}
		}

		$tagsCount = $tagQuery->get()->count();
		$tags = fractal(
			$tagQuery
				->orderBy('created_at', 'desc')
				->skip($offset)
				->take($limit)
				->get(),
			new TagTransformer()
		);
		return $this->respondSuccessWithPagination($tags, $tagsCount);
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
		$tagQuery = Tag::where('id', $id);
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
