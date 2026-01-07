<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Committee extends Model
{
    use HasFactory;

    protected $table = 'committee_categories';
    protected $primaryKey = 'committee_id';
    protected $fillable = [
        'added_by',
        'committee_name',
        'description',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function addedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'added_by');
    }

    public function reports(): HasMany
    {
        return $this->hasMany(CommitteeReport::class, 'committee_id', 'committee_id');
    }
}