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
        if (!Schema::hasTable('committee_reports')) {
            Schema::create('committee_reports', function (Blueprint $table) {
                $table->bigIncrements('report_id');
                $table->unsignedBigInteger('added_by');
                $table->string('committee_report_no')->nullable();
                $table->string('subject')->nullable();
                $table->text('title')->nullable();
                $table->integer('year')->nullable();
                $table->string('file_name')->nullable();
                $table->string('file_url')->nullable();
                $table->string('file_type')->nullable();
                $table->bigInteger('file_size')->nullable();
                $table->text('extracted_text')->nullable();
                $table->timestamp('created_at')->nullable()->useCurrent();
                $table->timestamp('updated_at')->nullable();
                $table->boolean('is_visible')->default(true);
                $table->unsignedBigInteger('last_modified_by')->nullable();
                $table->timestamp('last_modified_at')->nullable();

                $table->foreign('added_by')->references('id')->on('users')->onDelete('cascade');
                $table->foreign('last_modified_by')->references('id')->on('users')->onDelete('set null');
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
        Schema::dropIfExists('committee_reports');
    }
};