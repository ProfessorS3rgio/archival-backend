<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\LogsActivity;

class Ordinance extends Model
{
    use LogsActivity;
    
    protected $table = 'ordinance_lists';
    protected $primaryKey = 'ordinance_id';
    protected $fillable = [
        'ordinance_draft_id',
        'added_by', 
        'ordinance_no', 
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
        return $this->hasMany(OrdinanceFile::class, 'ordinance_id', 'ordinance_id');
    }

    public function adder()
    {
        return $this->belongsTo(User::class, 'added_by', 'id');
    }
    
    public function draft()
    {
        return $this->belongsTo(DraftOrdinance::class, 'ordinance_draft_id', 'ordinance_draft_id');
    }
}
