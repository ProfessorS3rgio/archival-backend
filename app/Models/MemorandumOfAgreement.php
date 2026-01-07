<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MemorandumOfAgreement extends Model
{
    use HasFactory;

    protected $table = 'moa_lists';
    protected $primaryKey = 'moa_id';
    protected $fillable = [
        'added_by',
        'resolution_no',
        'date_received',
        'title',
        'year',
        'file_name',
        'file_url',
        'file_size',
        'file_type',
        'date_uploaded',
        'extracted_text',
    ];

    protected $casts = [
        'date_received' => 'date',
        'date_uploaded' => 'datetime',
    ];

    public function addedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'added_by');
    }
}