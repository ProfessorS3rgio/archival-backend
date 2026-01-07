<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\LogsActivity;

class Resolution extends Model
{
    use LogsActivity;
    
    protected $table = 'resolution_lists';
    protected $primaryKey = 'resolution_id';
    protected $fillable = [
        'resolution_draft_id',
        'resolution_no', 
        'added_by', 
        'author', 
        'session', 
        'title', 
        'subject', 
        'date_approved',
        'is_municipal',
        'municipality',
        'extracted_text'
    ];
    
    protected $casts = [
        'date_approved' => 'date',
    ];

    public function files()
    {
        return $this->hasMany(ResolutionFile::class, 'resolution_id', 'resolution_id');
    }

    public function adder()
    {
        return $this->belongsTo(User::class, 'added_by', 'id');
    }
    
    public function draft()
    {
        return $this->belongsTo(DraftResolution::class, 'resolution_draft_id', 'resolution_draft_id');
    }
}
