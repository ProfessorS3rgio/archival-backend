<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MunicipalitiesResolution extends Model
{
    protected $table = 'municipalities_resolutions';
    protected $fillable = ['added_by', 'resolution_no', 'author', 'session', 'title', 'subject', 'date_approved'];
}
