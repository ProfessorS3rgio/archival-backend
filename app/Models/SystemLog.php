<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SystemLog extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'system_logs';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'log_id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'action',
        'target_table',
        'target_id',
        'description',
        'old_values',
        'new_values',
        'ip_address',
        'user_agent'
    ];

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'created_at' => 'datetime',
    ];

    /**
     * Get the user that performed the action.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the old values as an array
     * 
     * @return array|null
     */
    public function getOldValuesAttribute($value)
    {
        return $value ? json_decode($value, true) : null;
    }

    /**
     * Set the old values as JSON
     * 
     * @param array $value
     * @return void
     */
    public function setOldValuesAttribute($value)
    {
        $this->attributes['old_values'] = $value ? json_encode($value) : null;
    }

    /**
     * Get the new values as an array
     * 
     * @return array|null
     */
    public function getNewValuesAttribute($value)
    {
        return $value ? json_decode($value, true) : null;
    }

    /**
     * Set the new values as JSON
     * 
     * @param array $value
     * @return void
     */
    public function setNewValuesAttribute($value)
    {
        $this->attributes['new_values'] = $value ? json_encode($value) : null;
    }
}