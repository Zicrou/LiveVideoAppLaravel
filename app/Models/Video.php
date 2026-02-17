<?php

namespace App\Models;

use App\Models\Concerns\HasUuid;
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



    protected $casts = [
        'caption' => 'string',
        'video_url' => 'string',
        'owner_id' => 'string',
        'post_id' => 'string',
    ];
       
}
