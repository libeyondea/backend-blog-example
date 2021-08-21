<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\CustomController;
use Illuminate\Http\Request;
use App\Models\Article;
use App\Models\ArticleTag;
use App\Models\ArticleCategory;
use App\Models\Tag;
use App\Models\Category;
use App\Transformers\ArticleTransformer;
use App\Http\Requests\Admin\StoreArticleRequest;
use App\Http\Requests\Admin\UpdateArticleRequest;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;

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
		$sortDirection = $request->get('sort_direction', 'desc');

		$articleQuery = new Article();

		if ($request->has('sort_by')) {
			if ($request->sort_by === 'tags') {
				$articleQuery = $articleQuery->withCount('tags')->orderBy('tags_count', $sortDirection);
			} elseif ($request->sort_by === 'categories') {
				$articleQuery = $articleQuery->withCount('categories')->orderBy('categories_count', $sortDirection);
			} else {
				$articleQuery = $articleQuery->orderBy($request->sort_by, $sortDirection);
			}
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
	public function store(StoreArticleRequest $request)
	{
		$createArticle = new Article();
		$createArticle->user_id = auth()->user()->id;
		$createArticle->title = $request->title;
		$createArticle->content = $request->content;
		$createArticle->pinned = $request->pinned;
		$createArticle->published = $request->published;
		$createArticle->published_at = $request->published ? Carbon::now()->toDateTimeString() : null;
		$createArticle->excerpt = Str::limit(
			preg_replace('/\s+/', ' ', trim(strip_tags(Str::markdown($request->content)))),
			166,
			'...'
		);

		if ($request->slug) {
			$slug = Str::slug($request->slug, '-');
		} else {
			$slug = Str::slug($request->title, '-');
		}
		if (Article::where('slug', $slug)->exists()) {
			$slug = $slug . '-' . Str::lower(Str::random(4));
		}
		$createArticle->slug = $slug;

		// AWS S3
		//if ($request->hasfile('image')) {
		//	$imageName = time() . '.' . $request->file('image')->extension();
		//	Storage::disk('s3')->put('images/' . $imageName, file_get_contents($request->file('image')), 'public');
		//	$createArticle->image = $imageName;
		//}

		// Public folder
		if ($request->hasfile('image')) {
			$imageName = time() . '.' . $request->file('image')->extension();
			Storage::put($imageName, file_get_contents($request->file('image')));
			$createArticle->image = $imageName;
		}

		$createArticle->save();
		$lastIdArticle = $createArticle->id;

		foreach ($request->categories as $key => $category) {
			$categoryId = $category['id'];
			$checkArticleCategory = ArticleCategory::where('article_id', $lastIdArticle)
				->where('category_id', $categoryId)
				->first();
			if (!$checkArticleCategory) {
				$articleCategory = new ArticleCategory();
				$articleCategory->article_id = $lastIdArticle;
				$articleCategory->category_id = $categoryId;
				$articleCategory->save();
			}
		}

		foreach ($request->tags as $key => $tag) {
			if (isset($tag['is_new']) && $tag['is_new'] === true) {
				$convertTitleToSlug = Str::slug($tag['title'], '-');
				$newTag = new Tag();
				$newTag->title = $tag['title'];
				$newTag->content = $tag['title'];
				if (Tag::where('slug', $convertTitleToSlug)->exists()) {
					$convertTitleToSlug = $convertTitleToSlug . '-' . Str::lower(Str::random(4));
				}
				$newTag->slug = $convertTitleToSlug;
				$newTag->save();
				$tagId = $newTag->id;
			} else {
				$tagId = $tag['id'];
			}
			$checkArticleTag = ArticleTag::where('article_id', $lastIdArticle)
				->where('tag_id', $tagId)
				->first();
			if (!$checkArticleTag) {
				$articleTag = new ArticleTag();
				$articleTag->article_id = $lastIdArticle;
				$articleTag->tag_id = $tagId;
				$articleTag->save();
			}
		}

		$article = fractal(Article::where('id', $lastIdArticle)->firstOrFail(), new ArticleTransformer());
		return $this->respondSuccess($article);
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function show($id)
	{
		$articleQuery = Article::where('id', $id);
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
	public function update(UpdateArticleRequest $request, $id)
	{
		$updateArticle = Article::where('id', $id)->firstOrFail();
		$updateArticle->user_id = auth()->user()->id;
		$updateArticle->title = $request->title;
		$updateArticle->content = $request->content;
		$updateArticle->pinned = $request->pinned;
		$updateArticle->published = $request->published;
		$updateArticle->published_at = $request->published ? Carbon::now()->toDateTimeString() : null;
		$updateArticle->excerpt = Str::limit(
			preg_replace('/\s+/', ' ', trim(strip_tags(Str::markdown($request->content)))),
			166,
			'...'
		);

		if ($request->slug) {
			$slug = Str::slug($request->slug, '-');
		} else {
			$slug = Str::slug($request->title, '-');
		}
		if (
			Article::where('slug', $slug)
				->where('id', '!=', $id)
				->exists()
		) {
			$slug = $slug . '-' . Str::lower(Str::random(4));
		}
		$updateArticle->slug = $slug;

		/* if ($request->hasfile('image')) {
			$oldImage = 'images/' . $updateArticle->image;
			if (Storage::disk('s3')->exists($oldImage)) {
				Storage::disk('s3')->delete($oldImage);
			}
			$imageName = time() . '.' . $request->file('image')->extension();
			Storage::disk('s3')->put('images/' . $imageName, file_get_contents($request->file('image')), 'public');
			$updateArticle->image = $imageName;
		} */

		// Public folder
		if ($request->hasfile('image')) {
			$oldImage = $updateArticle->image;
			if (Storage::exists($oldImage)) {
				Storage::delete($oldImage);
			}
			$imageName = time() . '.' . $request->file('image')->extension();
			Storage::put($imageName, file_get_contents($request->file('image')));
			$updateArticle->image = $imageName;
		}

		$updateArticle->save();
		$lastIdArticle = $updateArticle->id;

		$deleteArticleTag = ArticleTag::where('article_id', $lastIdArticle);
		if ($deleteArticleTag->get()->count() > 0) {
			$deleteArticleTag->delete();
		}

		$deleteArticleCategory = ArticleCategory::where('article_id', $lastIdArticle);
		if ($deleteArticleCategory->get()->count() > 0) {
			$deleteArticleCategory->delete();
		}

		foreach ($request->categories as $key => $category) {
			$categoryId = $category['id'];
			$checkArticleCategory = ArticleCategory::where('article_id', $lastIdArticle)
				->where('category_id', $categoryId)
				->first();
			if (!$checkArticleCategory) {
				$articleCategory = new ArticleCategory();
				$articleCategory->article_id = $lastIdArticle;
				$articleCategory->category_id = $categoryId;
				$articleCategory->save();
			}
		}

		foreach ($request->tags as $key => $tag) {
			if (isset($tag['is_new']) && $tag['is_new'] === true) {
				$convertTitleToSlug = Str::slug($tag['title'], '-');
				$newTag = new Tag();
				$newTag->title = $tag['title'];
				$newTag->content = $tag['title'];
				if (Tag::where('slug', $convertTitleToSlug)->exists()) {
					$convertTitleToSlug = $convertTitleToSlug . '-' . Str::lower(Str::random(4));
				}
				$newTag->slug = $convertTitleToSlug;
				$newTag->save();
				$tagId = $newTag->id;
			} else {
				$tagId = $tag['id'];
			}
			$checkArticleTag = ArticleTag::where('article_id', $lastIdArticle)
				->where('tag_id', $tagId)
				->first();
			if (!$checkArticleTag) {
				$articleTag = new ArticleTag();
				$articleTag->article_id = $lastIdArticle;
				$articleTag->tag_id = $tagId;
				$articleTag->save();
			}
		}

		$article = fractal(Article::where('id', $lastIdArticle)->firstOrFail(), new ArticleTransformer());
		return $this->respondSuccess($article);
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function destroy($id)
	{
		$deleteArticle = Article::where('id', $id)->firstOrFail();
		$deleteArticle->delete();
		return $this->respondSuccess($deleteArticle);
	}
}
