<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\CustomController;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Transformers\CategoryTransformer;
use App\Http\Requests\Admin\StoreCategoryRequest;
use App\Http\Requests\Admin\UpdateCategoryRequest;
use Illuminate\Support\Str;

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

		if ($request->has('q')) {
			if ($request->q) {
				$categoryQuery = $categoryQuery
					->where('title', 'LIKE', '%' . $request->q . '%')
					->orWhere('slug', 'LIKE', '%' . $request->q . '%');
			}
		}

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
	public function store(StoreCategoryRequest $request)
	{
		$createCategory = new Category();
		$createCategory->title = $request->title;
		$createCategory->content = $request->content;

		if ($request->slug) {
			$slug = Str::slug($request->slug, '-');
		} else {
			$slug = Str::slug($request->title, '-');
		}
		if (Category::where('slug', $slug)->exists()) {
			$slug = $slug . '-' . Str::lower(Str::random(4));
		}
		$createCategory->slug = $slug;

		$createCategory->save();

		$category = fractal(Category::where('id', $createCategory->id)->firstOrFail(), new CategoryTransformer());
		return $this->respondSuccess($category);
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
	public function update(UpdateCategoryRequest $request, $id)
	{
		$updateCategory = Category::where('id', $id)->firstOrFail();
		$updateCategory->title = $request->title;
		$updateCategory->content = $request->content;

		if ($request->slug) {
			$slug = Str::slug($request->slug, '-');
		} else {
			$slug = Str::slug($request->title, '-');
		}
		if (
			Category::where('slug', $slug)
				->where('id', '!=', $id)
				->exists()
		) {
			$slug = $slug . '-' . Str::lower(Str::random(4));
		}
		$updateCategory->slug = $slug;

		$updateCategory->save();

		$category = fractal(Category::where('id', $updateCategory->id)->firstOrFail(), new CategoryTransformer());
		return $this->respondSuccess($category);
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function destroy($id)
	{
		$deleteCategory = Category::where('id', $id)->firstOrFail();
		$deleteCategory->delete();
		return $this->respondSuccess($deleteCategory);
	}
}
