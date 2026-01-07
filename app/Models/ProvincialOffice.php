<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProvincialOffice extends Model
{
    use HasFactory;

    protected $table = 'provincial_offices_list';
    protected $primaryKey = 'provincial_offices_list_id';
    protected $fillable = [
        'added_by',
        'year',
        'subject',
        'title',
        'Date_received',
        'file_name',
        'file_url',
        'file_size',
        'file_type',
        'extracted_text',
        'is_visible',
        'last_modified_by',
        'last_modified_at'
    ];

    protected $casts = [
        'Date_received' => 'date',
    ];

    public function addedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'added_by');
    }

    public function lastModifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'last_modified_by');
    }

    /**
     * Get the route key name for Laravel route model binding.
     */
    public function getRouteKeyName(): string
    {
        return 'provincial_offices_list_id';
    }
}