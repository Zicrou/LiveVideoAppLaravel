<?php

namespace App\Models;

use App\Models\Concerns\HasUuid;
use App\Models\Comment;
use Illuminate\Database\Eloquent\Model;

class Video extends Model
{
    use HasUuid;
    protected $fillable = [
        'caption',
        'video_url',
        'owner_id',
        'post_id',
    ];


    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }
    public function post()
    {
        return $this->belongsTo(Post::class, 'post_id');
    }

    public function likes()
    {
        return $this->hasMany(Like::class, 'video_id');
    }

    public function saveds()
    {
        return $this->hasMany(Save::class, 'video_id');
    }

    public function comments()
    {
        return $this->hasMany(Comment::class, 'video_id');
    }

    protected $casts = [
        'caption' => 'string',
        'video_url' => 'string',
        'owner_id' => 'string',
        'post_id' => 'string',
    ];
       
}
