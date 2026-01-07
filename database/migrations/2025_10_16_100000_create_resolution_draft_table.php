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
        if (!Schema::hasTable('resolution_draft')) {
            Schema::create('resolution_draft', function (Blueprint $table) {
                $table->bigIncrements('resolution_draft_id');
                $table->unsignedBigInteger('added_by');
                $table->integer('year')->nullable();
                $table->string('author')->nullable();
                $table->string('session')->nullable();
                $table->string('title')->nullable();
                $table->string('subject')->nullable();
                $table->timestamp('date_drafted')->nullable();
                $table->string('status')->nullable();
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
        Schema::dropIfExists('resolution_draft');
    }
};