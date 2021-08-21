<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\CustomController;
use Illuminate\Http\Request;
use App\Models\Tag;
use App\Transformers\TagTransformer;
use App\Http\Requests\Admin\StoreTagRequest;
use App\Http\Requests\Admin\UpdateTagRequest;
use Illuminate\Support\Str;

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
	public function store(StoreTagRequest $request)
	{
		$createTag = new Tag();
		$createTag->title = $request->title;
		$createTag->content = $request->content;

		if ($request->slug) {
			$slug = Str::slug($request->slug, '-');
		} else {
			$slug = Str::slug($request->title, '-');
		}
		if (Tag::where('slug', $slug)->exists()) {
			$slug = $slug . '-' . Str::lower(Str::random(4));
		}
		$createTag->slug = $slug;

		$createTag->save();

		$tag = fractal(Tag::where('id', $createTag->id)->firstOrFail(), new TagTransformer());
		return $this->respondSuccess($tag);
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
	public function update(UpdateTagRequest $request, $id)
	{
		$updateTag = Tag::where('id', $id)->firstOrFail();
		$updateTag->title = $request->title;
		$updateTag->content = $request->content;

		if ($request->slug) {
			$slug = Str::slug($request->slug, '-');
		} else {
			$slug = Str::slug($request->title, '-');
		}
		if (
			Tag::where('slug', $slug)
				->where('id', '!=', $id)
				->exists()
		) {
			$slug = $slug . '-' . Str::lower(Str::random(4));
		}
		$updateTag->slug = $slug;

		$updateTag->save();

		$tag = fractal(Tag::where('id', $updateTag->id)->firstOrFail(), new TagTransformer());
		return $this->respondSuccess($tag);
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return \Illuminate\Http\Response
	 */
	public function destroy($id)
	{
		$deleteTag = Tag::where('id', $id)->firstOrFail();
		$deleteTag->delete();
		return $this->respondSuccess($deleteTag);
	}
}
