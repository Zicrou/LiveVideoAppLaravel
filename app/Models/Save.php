<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\HasUuid;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Save extends Model
{
    use HasUuid, HasFactory;
    
    protected $table = 'saves';
    protected $fillable = [
        'owner_id',
        'user_id',
        'video_id',
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
