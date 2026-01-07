<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('ordinance_lists')) {
            Schema::create('ordinance_lists', function (Blueprint $table) {
                $table->bigIncrements('ordinance_id');
                $table->unsignedBigInteger('ordinance_draft_id')->nullable();
                $table->unsignedBigInteger('added_by');
                $table->string('ordinance_no')->nullable();
                $table->string('author')->nullable();
                $table->string('session')->nullable();
                $table->string('title')->nullable();
                $table->string('subject')->nullable();
                $table->date('date_approved')->nullable();
                $table->boolean('is_municipal')->default(false);
                $table->string('municipality')->nullable();
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

    public function down()
    {
        Schema::dropIfExists('ordinance_lists');
    }
};
