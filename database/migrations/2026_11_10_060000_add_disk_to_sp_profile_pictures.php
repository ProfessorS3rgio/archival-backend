<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('sp_profile_pictures', function (Blueprint $table) {
            // Pictures uploaded before this migration live on the local public disk.
            $table->string('disk', 20)->default('public');
        });
    }

    public function down(): void
    {
        if (DB::table('sp_profile_pictures')->where('disk', '!=', 'public')->exists()) {
            throw new RuntimeException('Move officials profile pictures back to the public disk before rolling back.');
        }

        Schema::table('sp_profile_pictures', fn (Blueprint $table) => $table->dropColumn('disk'));
    }
};
