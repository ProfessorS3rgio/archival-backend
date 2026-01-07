<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SangguniangPanlalawiganMember;
use App\Models\SangguniangPanlalawiganTerm;

class SangguniangPanlalawiganMembersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get the 9th term (2025-2028)
        $term = SangguniangPanlalawiganTerm::where('term_no', '9th')->first();

        if (!$term) {
            return; // Skip if term doesn't exist
        }

        $members = [
            // Vice Governor
            [
                'term_id' => $term->term_id,
                'district' => null,
                'full_name' => 'Hon. Maria Santos Reyes',
                'extension' => 'Hon',
                'position' => 'Vice Governor',
            ],

            // 1st District Board Members (5 members)
            [
                'term_id' => $term->term_id,
                'district' => '1st District',
                'full_name' => 'Atty. Juan Carlos Mendoza',
                'extension' => 'Atty',
                'position' => 'Board Member',
            ],
            [
                'term_id' => $term->term_id,
                'district' => '1st District',
                'full_name' => 'Hon. Ana Maria Cruz',
                'extension' => 'Hon',
                'position' => 'Board Member',
            ],
            [
                'term_id' => $term->term_id,
                'district' => '1st District',
                'full_name' => 'Doc. Roberto Garcia',
                'extension' => 'Doc',
                'position' => 'Board Member',
            ],
            [
                'term_id' => $term->term_id,
                'district' => '1st District',
                'full_name' => 'Hon. Elena Torres',
                'extension' => 'Hon',
                'position' => 'Board Member',
            ],
            [
                'term_id' => $term->term_id,
                'district' => '1st District',
                'full_name' => 'Atty. Miguel Santos',
                'extension' => 'Atty',
                'position' => 'Board Member',
            ],

            // 2nd District Board Members (5 members)
            [
                'term_id' => $term->term_id,
                'district' => '2nd District',
                'full_name' => 'Hon. Ricardo Bautista',
                'extension' => 'Hon',
                'position' => 'Board Member',
            ],
            [
                'term_id' => $term->term_id,
                'district' => '2nd District',
                'full_name' => 'Atty. Sofia Ramirez',
                'extension' => 'Atty',
                'position' => 'Board Member',
            ],
            [
                'term_id' => $term->term_id,
                'district' => '2nd District',
                'full_name' => 'Hon. Francisco Dela Cruz',
                'extension' => 'Hon',
                'position' => 'Board Member',
            ],
            [
                'term_id' => $term->term_id,
                'district' => '2nd District',
                'full_name' => 'Doc. Carmen Valdez',
                'extension' => 'Doc',
                'position' => 'Board Member',
            ],
            [
                'term_id' => $term->term_id,
                'district' => '2nd District',
                'full_name' => 'Atty. Luis Hernandez',
                'extension' => 'Atty',
                'position' => 'Board Member',
            ],

            // Ex-Officio Members (4 members)
            [
                'term_id' => $term->term_id,
                'district' => 'Ex-Officio',
                'full_name' => 'Atty. Jose Martinez',
                'extension' => 'Atty',
                'position' => 'Board Member',
            ],
            [
                'term_id' => $term->term_id,
                'district' => 'Ex-Officio',
                'full_name' => 'Doc. Maria Elena Lopez',
                'extension' => 'Doc',
                'position' => 'Board Member',
            ],
            [
                'term_id' => $term->term_id,
                'district' => 'Ex-Officio',
                'full_name' => 'Hon. Antonio Fernandez',
                'extension' => 'Hon',
                'position' => 'Board Member',
            ],
            [
                'term_id' => $term->term_id,
                'district' => 'Ex-Officio',
                'full_name' => 'Atty. Rosa Gonzales',
                'extension' => 'Atty',
                'position' => 'Board Member',
            ],
        ];

        foreach ($members as $member) {
            SangguniangPanlalawiganMember::create($member);
        }
    }
}