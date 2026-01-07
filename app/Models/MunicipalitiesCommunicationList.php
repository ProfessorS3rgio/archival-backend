<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MunicipalitiesCommunicationList extends Model
{
    use HasFactory;

    protected $table = 'municipalities_communication_lists';
    protected $primaryKey = 'municipalitites_communication_id';

    protected $fillable = [
        'added_by',
        'year',
        'date_received',
        'title',
        'subject',
        'file_name',
        'file_url',
        'file_size',
        'file_type',
        'date_uploaded',
        'extracted_text',
        'is_visible',
        'last_modified_by',
        'last_modified_at',
    ];

    protected $casts = [
        'date_received' => 'date',
        'date_uploaded' => 'datetime',
        'is_visible' => 'boolean',
        'last_modified_at' => 'datetime',
        'file_size' => 'integer',
    ];

    public function adder(): BelongsTo
    {
        return $this->belongsTo(User::class, 'added_by');
    }

    public function lastModifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'last_modified_by');
    }
}