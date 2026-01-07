<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DraftOrdinance extends Model
{
    use HasFactory;

    protected $table = 'ordinance_draft';
    protected $primaryKey = 'ordinance_draft_id';
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
        return $this->hasMany(DraftOrdinanceFile::class, 'ordinance_draft_id', 'ordinance_draft_id');
    }

    public function ordinances(): HasMany
    {
        return $this->hasMany(Ordinance::class, 'ordinance_draft_id', 'ordinance_draft_id');
    }
}
