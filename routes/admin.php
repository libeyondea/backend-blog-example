<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\ArticleController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\TagController;
use App\Http\Controllers\Admin\SearchController;
use App\Http\Controllers\Admin\AuthController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('login', [AuthController::class, 'login']);

Route::middleware('auth:api')->group(function () {
	Route::apiResources([
		'articles' => ArticleController::class,
		'categories' => CategoryController::class,
		'tags' => TagController::class
	]);

	Route::get('search', [SearchController::class, 'search']);

	Route::get('logout', [AuthController::class, 'logout']);
	Route::get('current-user', [AuthController::class, 'currentUser']);
});
