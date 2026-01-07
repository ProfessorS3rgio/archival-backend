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
        if (!Schema::hasTable('ordinance_draft')) {
            Schema::create('ordinance_draft', function (Blueprint $table) {
                $table->bigIncrements('ordinance_draft_id');
                $table->unsignedBigInteger('added_by');
                $table->integer('year')->nullable();
                $table->string('author')->nullable();
                $table->string('session')->nullable();
                $table->string('title')->nullable();
                $table->string('subject')->nullable();
                $table->timestamp('date_drafted')->nullable();
                $table->timestamp('created_at')->nullable()->useCurrent();
                $table->string('status')->nullable();
                $table->timestamp('updated_at')->nullable();

                $table->foreign('added_by')->references('id')->on('users')->onDelete('cascade');
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
        Schema::dropIfExists('ordinance_draft');
    }
};