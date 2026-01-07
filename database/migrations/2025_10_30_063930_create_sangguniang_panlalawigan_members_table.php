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
        Schema::create('sangguniang_panlalawigan_members', function (Blueprint $table) {
            $table->bigIncrements('member_id');
            $table->unsignedBigInteger('term_id');
            $table->enum('district', ['1st District', '2nd District', 'Ex-Officio'])->nullable();
            $table->string('full_name')->nullable();
            $table->enum('extension', ['Hon', 'Atty', 'Doc'])->nullable();
            $table->string('position')->nullable();
            $table->timestamp('created_at')->nullable()->useCurrent();

            $table->foreign('term_id')->references('term_id')->on('sangguniang_panlalawigan_terms')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sangguniang_panlalawigan_members');
    }
};
