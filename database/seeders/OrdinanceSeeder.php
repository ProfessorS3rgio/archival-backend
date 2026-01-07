<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class OrdinanceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // First, let's seed some ordinance lists
        $ordinanceIds = [];
        for ($i = 1; $i <= 5; $i++) {
            $ordinanceId = DB::table('ordinance_lists')->insertGetId([
                'added_by' => 1, // Assuming user ID 1 exists
                'ordinance_no' => $i,
                'author' => 'Author ' . $i,
                'session' => 'Regular Session ' . $i,
                'title' => 'Sample Ordinance ' . $i,
                'subject' => 'Subject for Ordinance ' . $i,
                'date_approved' => Carbon::now()->subDays(rand(1, 30)),
                'created_at' => Carbon::now()
            ]);
            $ordinanceIds[] = $ordinanceId;
        }

        // Now, let's seed one file for each ordinance
        foreach ($ordinanceIds as $ordinanceId) {
            DB::table('ordinance_files')->insert([
                'ordinance_id' => $ordinanceId,
                'uploaded_by' => 1, // Assuming user ID 1 exists
                'file_name' => "ordinance_{$ordinanceId}.pdf",
                'file_url' => "/storage/ordinances/{$ordinanceId}/ordinance.pdf",
                'file_type' => 'pdf',
                'uploaded_at' => Carbon::now()
            ]);
        }
    }
}