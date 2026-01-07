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
        if (!Schema::hasTable('national_offices_list')) {
            Schema::create('national_offices_list', function (Blueprint $table) {
                $table->bigIncrements('national_offices_id');
                $table->unsignedBigInteger('added_by');
                $table->string('provincial_resolution_no')->nullable();
                $table->integer('year')->nullable();
                $table->string('subject')->nullable();
                $table->date('date_received')->nullable();
                $table->string('title')->nullable();
                $table->string('file_name')->nullable();
                $table->string('file_url')->nullable();
                $table->unsignedBigInteger('file_size')->nullable();
                $table->string('file_type')->nullable();
                $table->timestamp('date_uploaded')->nullable()->useCurrent();
                $table->longText('extracted_text')->nullable();
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
        Schema::dropIfExists('national_offices_list');
    }
};