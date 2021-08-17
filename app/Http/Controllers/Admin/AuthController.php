<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\CustomController;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\User;

class AuthController extends CustomController
{
	public function login(Request $request)
	{
		$credentials = request(['user_name', 'password']);

		if (!auth()->attempt($credentials) || !auth()->user()->is_admin) {
			return $this->respondUnprocessableEntity('Incorrect username or password');
		}

		$tokenResult = auth()
			->user()
			->createToken('Personal Access Token');

		return $this->respondSuccess([
			'id' => auth()->user()->id,
			'user_name' => auth()->user()->user_name,
			'first_name' => auth()->user()->first_name,
			'last_name' => auth()->user()->last_name,
			'avatar' => config('custom.img_url') . '/' . auth()->user()->avatar,
			'token' => [
				'access_token' => $tokenResult->accessToken,
				'token_type' => 'Bearer',
				'expires_at' => Carbon::parse($tokenResult->token->expires_at)->toDateTimeString()
			]
		]);
	}

	public function logout(Request $request)
	{
		$user = auth()
			->user()
			->token();
		$user->revoke();
		return $this->respondSuccess('Logout success');
	}

	public function currentUser(Request $request)
	{
		return $this->respondSuccess([
			'id' => auth()->user()->id,
			'user_name' => auth()->user()->user_name,
			'first_name' => auth()->user()->first_name,
			'last_name' => auth()->user()->last_name,
			'avatar' => config('custom.img_url') . '/' . auth()->user()->avatar,
			'token' => [
				'access_token' => $request->bearerToken()
			]
		]);
	}
}
