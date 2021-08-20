<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\CustomController;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Transformers\CategoryTransformer;

class CategoryController extends CustomController
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

		$categoryQuery = new Category();

		if ($request->has('sort_by')) {
			if ($request->sort_by === 'total_articles') {
				$categoryQuery = $categoryQuery->withCount('articles')->orderBy('articles_count', $sortDirection);
			} else {
				$categoryQuery = $categoryQuery->orderBy($request->sort_by, $sortDirection);
			}
		}

		$categoriesCount = $categoryQuery->get()->count();

		$categories = fractal(
			$categoryQuery
				->orderBy('created_at', 'desc')
				->skip($offset)
				->take($limit)
				->get(),
			new CategoryTransformer()
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
		$categoryQuery = Category::where('id', $id);
		$category = fractal($categoryQuery->firstOrFail(), new CategoryTransformer());
		return $this->respondSuccess($category);
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
