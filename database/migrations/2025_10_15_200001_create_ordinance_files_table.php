<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('ordinance_files')) {
            Schema::create('ordinance_files', function (Blueprint $table) {
                $table->bigIncrements('file_id');
                $table->unsignedBigInteger('ordinance_id');
                $table->unsignedBigInteger('uploaded_by')->nullable();
                $table->integer('year')->nullable();
                $table->string('file_name')->nullable();
                $table->string('file_url')->nullable();
                $table->bigInteger('file_size')->nullable();
                $table->string('file_type')->nullable();
                $table->longText('extracted_text')->nullable(); // Store full extracted text for search
                $table->timestamp('uploaded_at')->nullable()->useCurrent();
                $table->timestamp('updated_at')->nullable();

                $table->foreign('ordinance_id')->references('ordinance_id')->on('ordinance_lists')->onDelete('cascade');
                $table->foreign('uploaded_by')->references('id')->on('users')->onDelete('set null');
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('ordinance_files');
    }
};
