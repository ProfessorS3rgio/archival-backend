<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NationalOfficeCommunicationList extends Model
{
    use HasFactory;

    protected $table = 'national_offices_list';
    protected $primaryKey = 'national_offices_id';
    protected $fillable = [
        'added_by',
        'provincial_resolution_no',
        'year',
        'subject',
        'date_received',
        'title',
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
        'date_received' => 'date',
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
        return 'national_offices_id';
    }
}