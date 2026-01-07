<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SangguniangPanlalawiganTerm;
use Carbon\Carbon;

class SangguniangPanlalawiganSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $terms = [
            ['term_no' => '1st', 'start_year' => '2001-01-01', 'end_year' => '2004-12-31'],
            ['term_no' => '2nd', 'start_year' => '2004-01-01', 'end_year' => '2007-12-31'],
            ['term_no' => '3rd', 'start_year' => '2007-01-01', 'end_year' => '2010-12-31'],
            ['term_no' => '4th', 'start_year' => '2010-01-01', 'end_year' => '2013-12-31'],
            ['term_no' => '5th', 'start_year' => '2013-01-01', 'end_year' => '2016-12-31'],
            ['term_no' => '6th', 'start_year' => '2016-01-01', 'end_year' => '2019-12-31'],
            ['term_no' => '7th', 'start_year' => '2019-01-01', 'end_year' => '2022-12-31'],
            ['term_no' => '8th', 'start_year' => '2022-01-01', 'end_year' => '2025-12-31'],
            ['term_no' => '9th', 'start_year' => '2025-01-01', 'end_year' => '2028-12-31'],
        ];

        foreach ($terms as $term) {
            SangguniangPanlalawiganTerm::create($term);
        }
    }
}
