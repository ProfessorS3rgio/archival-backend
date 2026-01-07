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
        Schema::create('sangguniang_panlalawigan_terms', function (Blueprint $table) {
            $table->bigIncrements('term_id');
            $table->string('term_no')->nullable();
            $table->date('start_year')->nullable();
            $table->date('end_year')->nullable();
            $table->timestamp('created_at')->nullable()->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sangguniang_panlalawigan_terms');
    }
};
