<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $param = [
            'name' => 'ユーザー1',
            'email' => 'user1@example.com',
            'email_verified_at' => Carbon::now(),
            'password' => Hash::make('password'),
            'role' => USER::ROLE_USER,
            'status' => USER::STATUS_OFF_DUTY,
        ];
        User::create($param);

        $param = [
            'name' => 'ユーザー2',
            'email' => 'user2@example.com',
            'email_verified_at' => Carbon::now(),
            'password' => Hash::make('password'),
            'role' => USER::ROLE_USER,
            'status' => USER::STATUS_OFF_DUTY,
        ];
        User::create($param);

        $param = [
            'name' => 'ユーザー3',
            'email' => 'user3@example.com',
            'email_verified_at' => Carbon::now(),
            'password' => Hash::make('password'),
            'role' => USER::ROLE_ADMIN,
            'status' => USER::STATUS_OFF_DUTY,
        ];
        User::create($param);
    }
}
