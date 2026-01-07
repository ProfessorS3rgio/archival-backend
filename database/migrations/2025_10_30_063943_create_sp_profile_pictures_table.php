<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('sp_profile_pictures', function (Blueprint $table) {
            $table->bigIncrements('sp_profile_pictures_id');
            $table->unsignedBigInteger('sp_member_id');
            $table->unsignedBigInteger('added_by');
            $table->string('file_name')->nullable();
            $table->string('file_url')->nullable();
            $table->string('file_type')->nullable();
            $table->timestamp('date_uploaded')->nullable()->useCurrent();

            $table->foreign('sp_member_id')->references('member_id')->on('sangguniang_panlalawigan_members')->onDelete('cascade');
            $table->foreign('added_by')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sp_profile_pictures');
    }
};
