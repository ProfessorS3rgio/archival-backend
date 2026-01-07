<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = [
        'auto_fill',
    ];

    protected $casts = [
        'auto_fill' => 'boolean',
    ];

    /**
     * Get the auto-fill setting value
     */
    public static function getAutoFillEnabled(): bool
    {
        $setting = self::first();
        return $setting ? $setting->auto_fill : true; // Default to true if no setting exists
    }

    /**
     * Set the auto-fill setting value
     */
    public static function setAutoFillEnabled(bool $enabled): void
    {
        $setting = self::first();
        if ($setting) {
            $setting->update(['auto_fill' => $enabled]);
        } else {
            self::create(['auto_fill' => $enabled]);
        }
    }
}
