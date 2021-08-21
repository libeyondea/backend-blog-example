<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\CustomController;
use Illuminate\Http\Request;
use App\Models\Article;
use App\Models\Tag;
use App\Transformers\ArticleTransformer;

class ArticleController extends CustomController
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
		$pinned = $request->get('pinned', 0);
		$articleQuery = Article::where('pinned', $pinned)->where('published', 1);
		if ($request->has('related')) {
			$articleQuery = $articleQuery
				->where(function ($subQuery) use ($request) {
					$subQuery
						->whereHas('categories', function ($q) use ($request) {
							$q->whereIn(
								'slug',
								Tag::whereHas('articles', function ($q) use ($request) {
									$q->where('slug', $request->related);
								})->pluck('slug')
							);
						})
						->orWhereHas('tags', function ($q) use ($request) {
							$q->whereIn(
								'slug',
								Tag::whereHas('articles', function ($q) use ($request) {
									$q->where('slug', $request->related);
								})->pluck('slug')
							);
						});
				})
				->where('slug', '!=', $request->related);
		} elseif ($request->has('tag')) {
			$articleQuery = $articleQuery->whereHas('tags', function ($q) use ($request) {
				$q->where('slug', $request->tag);
			});
		} elseif ($request->has('category')) {
			$articleQuery = $articleQuery->whereHas('categories', function ($q) use ($request) {
				$q->where('slug', $request->category);
			});
		}
		$articlesCount = $articleQuery->get()->count();
		$articles = fractal(
			$articleQuery
				->orderBy('created_at', 'desc')
				->skip($offset)
				->take($limit)
				->get(),
			new ArticleTransformer()
		);
		return $this->respondSuccessWithPagination($articles, $articlesCount);
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
		$articleQuery = Article::where('slug', $id)->where('published', 1);
		$article = fractal($articleQuery->firstOrFail(), new ArticleTransformer());
		return $this->respondSuccess($article);
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
