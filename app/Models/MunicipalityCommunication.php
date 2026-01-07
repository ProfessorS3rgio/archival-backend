<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MunicipalityCommunication extends Model
{
    use HasFactory;

    protected $table = 'municipalities_communication_lists';
    protected $primaryKey = 'municipalitites_communication_id';
    protected $fillable = [
        'added_by',
        'municipal_ordinance_no',
        'municipal_resolution_no',
        'provincial_resolution_no',
        'committe_no',
        'year',
        'Municipality',
        'date_received',
        'title'
    ];

    protected $casts = [
        'date_received' => 'date',
    ];

    public function addedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'added_by');
    }
}