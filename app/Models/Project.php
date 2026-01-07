<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Project extends Model
{
    use HasFactory;

    protected $table = 'project_lists';
    protected $primaryKey = 'project_id';
    protected $fillable = [
        'added_by',
        'code_no',
        'district',
        'location',
        'project_name',
        'year',
        'source_fund',
        'source_type',
        'amount',
        'file_name',
        'file_url',
        'file_size',
        'file_type',
        'extracted_text',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    public function addedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'added_by');
    }
}