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
        if (!Schema::hasTable('project_lists')) {
            Schema::create('project_lists', function (Blueprint $table) {
                $table->bigIncrements('project_id');
                $table->unsignedBigInteger('added_by');
                $table->integer('code_no')->nullable();
                $table->string('district')->nullable();
                $table->string('location')->nullable();
                $table->string('project_name')->nullable();
                $table->string('source_fund')->nullable();
                $table->string('source_type')->nullable();
                $table->decimal('amount', 12, 2)->nullable();
                $table->string('file_name')->nullable();
                $table->string('file_url')->nullable();
                $table->bigInteger('file_size')->nullable();
                $table->string('file_type')->nullable();
                $table->longText('extracted_text')->nullable();
                $table->timestamp('created_at')->nullable()->useCurrent();
                $table->timestamp('updated_at')->nullable();
                $table->boolean('is_visible')->default(true);
                $table->unsignedBigInteger('last_modified_by')->nullable();
                $table->timestamp('last_modified_at')->nullable();
                $table->year('year')->nullable();

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
        Schema::dropIfExists('project_lists');
    }
};