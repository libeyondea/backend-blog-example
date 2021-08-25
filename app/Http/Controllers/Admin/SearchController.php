<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\CustomController;
use Illuminate\Http\Request;
use App\Models\Tag;
use App\Models\Category;
use App\Models\Article;
use App\Transformers\TagTransformer;
use App\Transformers\ArticleTransformer;
use App\Transformers\CategoryTransformer;

class SearchController extends CustomController
{
	public function search(Request $request)
	{
		$offset = $request->input('offset', 0);
		$limit = $request->input('limit', 10);
		$type = $request->input('type', '');
		$q = $request->input('q', '');

		if ($type == 'tag') {
			$model = Tag::where('title', 'LIKE', '%' . $q . '%')
				->orWhere('slug', 'LIKE', '%' . $q . '%')
				->withCount('articles')
				->orderBy('articles_count', 'desc');
			$transformer = new TagTransformer();
		} elseif ($type == 'category') {
			$model = Category::where('title', 'LIKE', '%' . $q . '%')
				->orWhere('slug', 'LIKE', '%' . $q . '%')
				->withCount('articles')
				->orderBy('articles_count', 'desc');
			$transformer = new CategoryTransformer();
		} elseif ($type == 'article') {
			$model = Article::where('title', 'LIKE', '%' . $q . '%')
				->orWhere('slug', 'LIKE', '%' . $q . '%')
				->orderBy('created_at', 'desc');
			$transformer = new ArticleTransformer();
		} else {
			return $this->respondNotFound();
		}

		$totalCount = $model->get()->count();

		$listModel = fractal(
			$model
				->skip($offset)
				->take($limit)
				->get(),
			$transformer
		);
		return $this->respondSuccessWithPagination($listModel, $totalCount);
	}
}
