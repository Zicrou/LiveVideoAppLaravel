<?php

namespace App\Models;

use App\Models\Concerns\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Like extends Model
{
    use HasUuid, HasFactory;
    protected $fillable = [
        'owner_id',
        'user_id',
        'video_id',
        'likeCount',
    ];

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }   

    public function video()
    {
        return $this->belongsTo(Video::class, 'video_id');
    }

}
