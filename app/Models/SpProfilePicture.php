<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SpProfilePicture extends Model
{
    use HasFactory;

    protected $table = 'sp_profile_pictures';
    protected $primaryKey = 'sp_profile_pictures_id';
    public $timestamps = false;

    protected $appends = ['display_url'];

    protected $fillable = [
        'sp_member_id',
        'added_by',
        'file_name',
        'file_url',
        'file_type',
        'disk',
    ];

    protected $casts = [
        'date_uploaded' => 'datetime',
    ];

    public function getDisplayUrlAttribute(): string
    {
        return route('sp.profile-picture', ['picture' => $this->getKey()], false);
    }

    public function member(): BelongsTo
    {
        return $this->belongsTo(SangguniangPanlalawiganMember::class, 'sp_member_id', 'member_id');
    }

    public function addedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'added_by');
    }
}
