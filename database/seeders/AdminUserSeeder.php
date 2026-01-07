<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class AdminUserSeeder extends Seeder
{
    public function run()
    {
        DB::table('users')->insert([
            'name' => 'Administrator',
            'email' => 'admin@example.com',
            'username' => 'admin',
            'role' => 'admin',
            'password' => Hash::make('123123'),
            'created_at' => now(),
        ]);
    }
}
