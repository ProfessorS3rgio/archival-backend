<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Index columns used by the document API. The guards keep this migration
     * safe for deployments with older table variants.
     */
    private array $indexes = [
        'resolution_lists' => ['is_visible', 'date_approved', 'added_by'],
        'ordinance_lists' => ['is_visible', 'date_approved', 'added_by'],
        'resolution_draft' => ['is_visible', 'year', 'added_by'],
        'ordinance_draft' => ['year', 'added_by'],
        'accomplish_lists' => ['is_visible', 'accomplishment_year', 'uploaded_by'],
        'project_lists' => ['is_visible', 'year', 'added_by'],
        'moa_lists' => ['is_visible', 'year', 'date_received', 'added_by'],
        'municipalities_communication_lists' => ['is_visible', 'year', 'date_received', 'added_by'],
        'national_offices_list' => ['is_visible', 'year', 'date_received', 'added_by'],
        'provincial_offices_list' => ['is_visible', 'year', 'date_received', 'added_by'],
        'committee_reports' => ['is_visible', 'year', 'added_by'],
        'committee_categories' => ['added_by'],
        'resolution_files' => ['uploaded_by'],
        'ordinance_files' => ['uploaded_by'],
        'resolution_draft_files' => ['uploaded_by'],
        'ordinance_draft_files' => ['uploaded_by'],
    ];

    public function up(): void
    {
        foreach ($this->indexes as $table => $columns) {
            if (! Schema::hasTable($table)) {
                continue;
            }

            foreach ($columns as $column) {
                if (! Schema::hasColumn($table, $column)) {
                    continue;
                }

                $name = 'api_'.$column;
                if (! Schema::hasIndex($table, $name)) {
                    Schema::table($table, fn (Blueprint $blueprint) => $blueprint->index($column, $name));
                }
            }
        }
    }

    public function down(): void
    {
        foreach ($this->indexes as $table => $columns) {
            if (! Schema::hasTable($table)) {
                continue;
            }

            foreach ($columns as $column) {
                $name = 'api_'.$column;
                if (Schema::hasIndex($table, $name)) {
                    Schema::table($table, fn (Blueprint $blueprint) => $blueprint->dropIndex($name));
                }
            }
        }
    }
};
