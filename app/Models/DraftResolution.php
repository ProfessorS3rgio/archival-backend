<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DraftResolution extends Model
{
    use HasFactory;

    protected $table = 'resolution_draft';
    protected $primaryKey = 'resolution_draft_id';
    protected $fillable = [
        'added_by',
        'year',
        'author',
        'session',
        'title',
        'subject',
        'date_drafted',
        'status'
    ];

    protected $casts = [
        'date_drafted' => 'datetime',
    ];

    public function addedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'added_by');
    }

    public function files(): HasMany
    {
        return $this->hasMany(DraftResolutionFile::class, 'resolution_draft_id', 'resolution_draft_id');
    }

    public function resolutions(): HasMany
    {
        return $this->hasMany(Resolution::class, 'resolution_draft_id', 'resolution_draft_id');
    }
}
