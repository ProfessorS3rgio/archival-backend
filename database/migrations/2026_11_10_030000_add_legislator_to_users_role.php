<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['admin', 'staff', 'user', 'legislator'])->default('user')->change();
        });
    }

    public function down(): void
    {
        // Refuse to truncate roles or silently change existing users' permissions.
        if (DB::table('users')->where('role', 'legislator')->exists()) {
            throw new RuntimeException('Reassign legislator accounts before rolling back the users role migration.');
        }

        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['admin', 'staff', 'user'])->default('user')->change();
        });
    }
};
