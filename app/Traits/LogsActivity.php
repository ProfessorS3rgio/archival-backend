<?php

namespace App\Traits;

use App\Services\LoggingService;

trait LogsActivity
{
    /**
     * Boot the trait.
     *
     * @return void
     */
    public static function bootLogsActivity()
    {
        static::created(function ($model) {
            if (!self::isLoggingDisabled()) {
                LoggingService::logCreate($model);
            }
        });

        static::updated(function ($model) {
            if (!self::isLoggingDisabled()) {
                // Get the original values before the update
                $oldValues = $model->getOriginal();
                LoggingService::logUpdate($model, $oldValues);
            }
        });

        static::deleted(function ($model) {
            if (!self::isLoggingDisabled()) {
                LoggingService::logDelete($model);
            }
        });
    }

    /**
     * Check if logging is temporarily disabled
     *
     * @return bool
     */
    public static function isLoggingDisabled()
    {
        return app()->bound('disable_activity_logging') && app('disable_activity_logging') === true;
    }

    /**
     * Temporarily disable logging for a callback
     *
     * @param callable $callback
     * @return mixed
     */
    public static function withoutLogging(callable $callback)
    {
        app()->instance('disable_activity_logging', true);
        
        try {
            return $callback();
        } finally {
            app()->instance('disable_activity_logging', false);
        }
    }
}