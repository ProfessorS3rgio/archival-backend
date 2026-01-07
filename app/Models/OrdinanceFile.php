<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrdinanceFile extends Model
{
    protected $table = 'ordinance_files';
    protected $primaryKey = 'file_id';
    protected $fillable = ['ordinance_id', 'uploaded_by', 'file_name', 'file_url', 'file_size', 'file_type', 'extracted_text'];

    const CREATED_AT = 'uploaded_at';
    const UPDATED_AT = 'updated_at';

    public function ordinance()
    {
        return $this->belongsTo(Ordinance::class, 'ordinance_id', 'ordinance_id');
    }

    public function uploader()
    {
        return $this->belongsTo(User::class, 'uploaded_by', 'id');
    }
}