<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class UserSeeder extends Seeder
{
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		User::factory(10)->create();
		User::insert([
			'first_name' => 'Thuc',
			'last_name' => 'Nguyen',
			'avatar' => '6666666666.png',
			'user_name' => 'admin',
			'email' => 'nguyenthucofficial@gmail.com',
			'email_verified_at' => now(),
			'password' => bcrypt('admin'),
			'remember_token' => null,
			'role' => 'administrator',
			'created_at' => now(),
			'updated_at' => now()
		]);
	}
}
