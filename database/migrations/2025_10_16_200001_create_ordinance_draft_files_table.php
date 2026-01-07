<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('ordinance_draft_files')) {
            Schema::create('ordinance_draft_files', function (Blueprint $table) {
                $table->bigIncrements('file_id');
                $table->unsignedBigInteger('ordinance_draft_id');
                $table->unsignedBigInteger('uploaded_by')->nullable();
                $table->string('file_name')->nullable();
                $table->string('file_url')->nullable();
                $table->bigInteger('file_size')->nullable();
                $table->string('file_type')->nullable();
                $table->longText('extracted_text')->nullable(); // Store full extracted text for search
                $table->timestamp('uploaded_at')->nullable()->useCurrent();
                $table->timestamp('created_at')->nullable()->useCurrent();
                $table->timestamp('updated_at')->nullable();

                $table->foreign('ordinance_draft_id')->references('ordinance_draft_id')->on('ordinance_draft')->onDelete('cascade');
                $table->foreign('uploaded_by')->references('id')->on('users')->onDelete('set null');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ordinance_draft_files');
    }
};