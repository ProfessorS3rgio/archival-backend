<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DraftOrdinanceFile extends Model
{
    use HasFactory;

    protected $table = 'ordinance_draft_files';
    protected $primaryKey = 'file_id';
    protected $fillable = [
        'ordinance_draft_id',
        'uploaded_by',
        'file_name',
        'file_url',
        'file_type',
        'file_size',
        'extracted_text',
        'uploaded_at'
    ];

    protected $casts = [
        'uploaded_at' => 'datetime',
    ];

    public function draftOrdinance(): BelongsTo
    {
        return $this->belongsTo(DraftOrdinance::class, 'ordinance_draft_id', 'ordinance_draft_id');
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}