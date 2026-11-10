<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Retain existing tables, IDs, metadata and file paths for deployed records.
        // Both existing databases and fresh installations receive the columns here.
        foreach (['accomplish_lists', 'project_lists'] as $tableName) {
            foreach (['respondent', 'complainant'] as $column) {
                if (! Schema::hasColumn($tableName, $column)) {
                    Schema::table($tableName, function (Blueprint $table) use ($column) {
                        $table->string($column)->nullable();
                    });
                }
            }
        }
    }

    public function down(): void
    {
        foreach (['accomplish_lists', 'project_lists'] as $tableName) {
            foreach (['respondent', 'complainant'] as $column) {
                if (Schema::hasColumn($tableName, $column)) {
                    Schema::table($tableName, function (Blueprint $table) use ($column) {
                        $table->dropColumn($column);
                    });
                }
            }
        }
    }
};
