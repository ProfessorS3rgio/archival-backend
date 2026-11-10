<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement(
            "ALTER TABLE sangguniang_panlalawigan_members " .
            "MODIFY extension ENUM('Hon', 'Atty', 'Judge', 'Doc') NULL"
        );
    }

    public function down(): void
    {
        if (DB::table('sangguniang_panlalawigan_members')
            ->where('extension', 'Judge')
            ->exists()) {
            throw new RuntimeException(
                'Reassign SP officials with the Judge extension before rolling back.'
            );
        }

        DB::statement(
            "ALTER TABLE sangguniang_panlalawigan_members " .
            "MODIFY extension ENUM('Hon', 'Atty', 'Doc') NULL"
        );
    }
};
