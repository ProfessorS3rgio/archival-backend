<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class SangguniangPanlalawiganMember extends Model
{
    use HasFactory;

    protected $table = 'sangguniang_panlalawigan_members';
    protected $primaryKey = 'member_id';
    public $timestamps = false;

    protected $fillable = [
        'term_id',
        'district',
        'full_name',
        'extension',
        'position',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public function term(): BelongsTo
    {
        return $this->belongsTo(SangguniangPanlalawiganTerm::class, 'term_id', 'term_id');
    }

    public function profilePicture(): HasOne
    {
        return $this->hasOne(SpProfilePicture::class, 'sp_member_id', 'member_id');
    }
}
