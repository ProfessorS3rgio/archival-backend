<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ResolutionSeeder extends Seeder
{
    public function run()
    {
        $user = DB::table('users')->where('username', 'admin')->first();

        $resolutionId = DB::table('resolution_lists')->insertGetId([
            'resolution_no' => 1,
            'added_by' => $user ? $user->id : null,
            'author' => 'Council Board',
            'session' => '2025Q4',
            'title' => 'Approve budget allocation',
            'date_approved' => now(),
            'created_at' => now(),
        ]);

        DB::table('resolution_files')->insert([
            'resolution_id' => $resolutionId,
            'uploaded_by' => $user ? $user->id : null,
            'file_name' => 'budget.pdf',
            'file_url' => '/files/budget.pdf',
            'file_type' => 'pdf',
            'uploaded_at' => now(),
        ]);
    }
}
