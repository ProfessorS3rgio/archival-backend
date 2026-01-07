<?php

namespace App\Services;

use App\Models\SystemLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

class LoggingService
{
    /**
     * Log a create action
     * 
     * @param Model $model The created model
     * @param string|null $description Additional description
     * @return SystemLog The created log entry
     */
    public static function logCreate(Model $model, ?string $description = null): SystemLog
    {
        return self::log('create', $model, null, $model->getAttributes(), $description);
    }

    /**
     * Log an update action
     * 
     * @param Model $model The updated model
     * @param array $oldValues The old values before update
     * @param string|null $description Additional description
     * @return SystemLog The created log entry
     */
    public static function logUpdate(Model $model, array $oldValues, ?string $description = null): SystemLog
    {
        return self::log('update', $model, $oldValues, $model->getAttributes(), $description);
    }

    /**
     * Log a delete action
     * 
     * @param Model $model The deleted model
     * @param string|null $description Additional description
     * @return SystemLog The created log entry
     */
    public static function logDelete(Model $model, ?string $description = null): SystemLog
    {
        return self::log('delete', $model, $model->getAttributes(), null, $description);
    }

    /**
     * Log a view action
     *
     * @param Model $model The viewed model
     * @param string|null $description Additional description
     * @return SystemLog The created log entry
     */
    public static function logView(Model $model, ?string $description = null): SystemLog
    {
        return self::log('view', $model, null, null, $description);
    }

    /**
     * Log a download action
     *
     * @param Model $model The downloaded model
     * @param string|null $description Additional description
     * @return SystemLog The created log entry
     */
    public static function logDownload(Model $model, ?string $description = null): SystemLog
    {
        return self::log('download', $model, null, null, $description);
    }    /**
     * Log a custom action
     * 
     * @param string $action The action performed
     * @param string $targetTable The table name
     * @param int|null $targetId The target ID
     * @param array|null $oldValues The old values
     * @param array|null $newValues The new values
     * @param string|null $description Additional description
     * @return SystemLog The created log entry
     */
    public static function logCustom(
        string $action,
        string $targetTable,
        ?int $targetId = null,
        ?array $oldValues = null,
        ?array $newValues = null,
        ?string $description = null
    ): SystemLog {
        return self::createLog($action, $targetTable, $targetId, $oldValues, $newValues, $description);
    }

    /**
     * General log method for all actions
     * 
     * @param string $action The action performed
     * @param Model $model The model affected
     * @param array|null $oldValues The old values
     * @param array|null $newValues The new values
     * @param string|null $description Additional description
     * @return SystemLog The created log entry
     */
    private static function log(
        string $action,
        Model $model,
        ?array $oldValues,
        ?array $newValues,
        ?string $description
    ): SystemLog {
        $table = $model->getTable();
        $id = $model->getKey();
        
        return self::createLog($action, $table, $id, $oldValues, $newValues, $description);
    }

    /**
     * Create the actual log entry
     * 
     * @param string $action The action performed
     * @param string $targetTable The table name
     * @param int|null $targetId The target ID
     * @param array|null $oldValues The old values
     * @param array|null $newValues The new values
     * @param string|null $description Additional description
     * @return SystemLog The created log entry
     */
    private static function createLog(
        string $action,
        string $targetTable,
        ?int $targetId,
        ?array $oldValues,
        ?array $newValues,
        ?string $description
    ): SystemLog {
        return SystemLog::create([
            'user_id' => Auth::id(),
            'action' => $action,
            'target_table' => $targetTable,
            'target_id' => $targetId,
            'description' => $description,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent()
        ]);
    }

    /**
     * Rollback a deletion based on a log entry
     * 
     * @param SystemLog $log The log entry for the deletion
     * @return Model|null The restored model, if successful
     */
    public static function rollbackDelete(SystemLog $log)
    {
        // Check if this is a delete action with old values
        if ($log->action !== 'delete' || !$log->old_values) {
            return null;
        }
        
        $targetTable = $log->target_table;
        
        // Get the model class name based on table name
        $modelClass = self::getModelClassFromTable($targetTable);
        if (!$modelClass) {
            return null;
        }
        
        try {
            // Create a new model instance with the old values
            $model = new $modelClass();
            $model->fill($log->old_values);
            
            // If the model uses soft deletes, undelete it
            if (in_array('Illuminate\Database\Eloquent\SoftDeletes', class_uses($modelClass))) {
                // For soft deletes, find the deleted record and restore it
                $found = $modelClass::withTrashed()->find($log->target_id);
                if ($found) {
                    $found->restore();
                    return $found;
                }
            }
            
            // For hard deletes, create a new record with the same values
            $model->save();
            
            // Log the restoration
            self::logCustom(
                'rollback',
                $targetTable,
                $model->getKey(),
                null,
                $model->getAttributes(),
                "Restored deleted record from log {$log->log_id}"
            );
            
            return $model;
        } catch (\Exception $e) {
            \Log::error("Failed to rollback deletion: {$e->getMessage()}", [
                'log_id' => $log->log_id,
                'exception' => $e
            ]);
            return null;
        }
    }
    
    /**
     * Try to get the model class name from a table name
     * 
     * @param string $tableName
     * @return string|null
     */
    private static function getModelClassFromTable(string $tableName): ?string
    {
        // Map common table names to model classes
        $tableToModelMap = [
            'resolution_lists' => \App\Models\Resolution::class,
            'resolution_files' => \App\Models\ResolutionFile::class,
            'ordinance_lists' => \App\Models\Ordinance::class,
            'ordinance_files' => \App\Models\OrdinanceFile::class,
            'resolution_draft' => \App\Models\DraftResolution::class,
            'resolution_draft_files' => \App\Models\DraftResolutionFile::class,
            'ordinance_draft' => \App\Models\DraftOrdinance::class,
            'ordinance_draft_files' => \App\Models\DraftOrdinanceFile::class,
            'moa_lists' => \App\Models\MemorandumOfAgreement::class,
            'municipalities_communication_lists' => \App\Models\MunicipalityCommunication::class,
            'committee_categories' => \App\Models\Committee::class,
            'committee_reports' => \App\Models\CommitteeReport::class,
            'accomplish_lists' => \App\Models\Accomplishment::class,
            'project_lists' => \App\Models\Project::class,
            'national_offices_list' => \App\Models\NationalOffice::class,
            'provincial_offices_list' => \App\Models\ProvincialOffice::class,
            'users' => \App\Models\User::class,
            // Add more mappings as you create more models
        ];
        
        return $tableToModelMap[$tableName] ?? null;
    }
}