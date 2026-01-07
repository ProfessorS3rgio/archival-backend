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
        if (!Schema::hasTable('system_logs')) {
            Schema::create('system_logs', function (Blueprint $table) {
                $table->bigIncrements('log_id');
                $table->unsignedBigInteger('user_id')->nullable();
                $table->string('action', 100);
                $table->string('target_table', 255)->nullable();
                $table->unsignedBigInteger('target_id')->nullable();
                $table->text('description')->nullable();
                $table->text('old_values')->nullable()->comment('JSON encoded old values for potential rollback');
                $table->text('new_values')->nullable()->comment('JSON encoded new values');
                $table->string('ip_address', 100)->nullable();
                $table->text('user_agent')->nullable();
                $table->timestamp('created_at')->nullable()->useCurrent();
                
                $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
                
                // Index for faster queries by action and target
                $table->index(['action', 'target_table', 'target_id']);
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
        Schema::dropIfExists('system_logs');
    }
};