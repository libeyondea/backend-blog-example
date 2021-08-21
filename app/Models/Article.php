<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
	use HasFactory;

	public function user()
	{
		return $this->belongsTo(User::class, 'user_id', 'id');
	}

	public function categories()
	{
		return $this->belongsToMany(Category::class, 'article_categories');
	}

	public function tags()
	{
		return $this->belongsToMany(Tag::class, 'article_tags');
	}
}
