<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Accomplishment extends Model
{
    use HasFactory;

    protected $table = 'accomplish_lists';
    protected $primaryKey = 'accomplish_id';
    protected $fillable = [
        'uploaded_by',
        'title',
        'accomplishment_year',
        'file_name',
        'file_url',
        'file_size',
        'file_type',
        'date_uploaded',
        'extracted_text',
    ];

    protected $casts = [
        'accomplishment_year' => 'date',
        'date_uploaded' => 'datetime',
    ];

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}