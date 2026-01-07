<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\LogsActivity;

class CommitteeReport extends Model
{
    use HasFactory, LogsActivity;

    protected $table = 'committee_reports';
    protected $primaryKey = 'report_id';
    protected $fillable = [
        'added_by',
        'committee_report_no',
        'subject',
        'title',
        'year',
        'file_name',
        'file_url',
        'file_type',
        'file_size',
        'extracted_text'
    ];

    public function addedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'added_by');
    }
}