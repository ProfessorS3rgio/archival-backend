<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ResolutionFile extends Model
{
    protected $table = 'resolution_files';
    protected $primaryKey = 'file_id';
    protected $fillable = ['resolution_id', 'uploaded_by', 'file_name', 'file_url', 'file_size', 'file_type', 'extracted_text'];

    const CREATED_AT = 'uploaded_at';
    const UPDATED_AT = 'updated_at';

    public function resolution()
    {
        return $this->belongsTo(Resolution::class, 'resolution_id', 'resolution_id');
    }

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by', 'id');
    }
}