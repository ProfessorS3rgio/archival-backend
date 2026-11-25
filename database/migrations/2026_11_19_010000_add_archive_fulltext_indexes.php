<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Search fields used by the archive and document APIs.
     * The column checks keep this additive migration safe for older databases.
     */
    private array $indexes = [
        'resolution_lists' => ['resolution_no', 'title', 'subject', 'author', 'extracted_text'],
        'ordinance_lists' => ['ordinance_no', 'title', 'subject', 'author', 'extracted_text'],
        'resolution_draft' => ['title', 'subject', 'author'],
        'ordinance_draft' => ['title', 'subject', 'author'],
        'resolution_files' => ['file_name', 'extracted_text'],
        'ordinance_files' => ['file_name', 'extracted_text'],
        'resolution_draft_files' => ['file_name', 'extracted_text'],
        'ordinance_draft_files' => ['file_name', 'extracted_text'],
        'accomplish_lists' => ['title', 'respondent', 'complainant', 'extracted_text'],
        'project_lists' => ['location', 'project_name', 'source_fund', 'respondent', 'complainant', 'extracted_text'],
        'committee_reports' => ['committee_report_no', 'subject', 'title', 'file_name', 'extracted_text'],
        'moa_lists' => ['resolution_no', 'title', 'file_name', 'extracted_text'],
        'municipalities_communication_lists' => ['title', 'subject', 'file_name', 'extracted_text'],
        'national_offices_list' => ['provincial_resolution_no', 'subject', 'title', 'file_name', 'extracted_text'],
        'provincial_offices_list' => ['title', 'subject', 'file_name', 'extracted_text'],
    ];

    public function up(): void
    {
        if (! in_array(Schema::getConnection()->getDriverName(), ['mysql', 'mariadb'], true)) {
            return;
        }

        foreach ($this->indexes as $table => $columns) {
            if (! Schema::hasTable($table)) {
                continue;
            }

            $available = array_values(array_filter($columns, fn (string $column): bool => Schema::hasColumn($table, $column)));
            $name = 'ft_'.$table.'_search';
            if ($available !== [] && ! Schema::hasIndex($table, $name)) {
                Schema::table($table, fn (Blueprint $blueprint) => $blueprint->fullText($available, $name));
            }
        }
    }

    public function down(): void
    {
        if (! in_array(Schema::getConnection()->getDriverName(), ['mysql', 'mariadb'], true)) {
            return;
        }

        foreach (array_keys($this->indexes) as $table) {
            $name = 'ft_'.$table.'_search';
            if (Schema::hasTable($table) && Schema::hasIndex($table, $name)) {
                Schema::table($table, fn (Blueprint $blueprint) => $blueprint->dropIndex($name));
            }
        }
    }
};
