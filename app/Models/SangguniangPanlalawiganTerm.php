<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SangguniangPanlalawiganTerm extends Model
{
    use HasFactory;

    protected $table = 'sangguniang_panlalawigan_terms';
    protected $primaryKey = 'term_id';
    public $timestamps = false;

    protected $fillable = [
        'term_no',
        'start_year',
        'end_year',
    ];

    protected $casts = [
        'start_year' => 'date',
        'end_year' => 'date',
        'created_at' => 'datetime',
    ];

    public function members(): HasMany
    {
        return $this->hasMany(SangguniangPanlalawiganMember::class, 'term_id', 'term_id');
    }
}
