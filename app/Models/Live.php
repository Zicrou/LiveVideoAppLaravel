<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Live extends Model
{
    protected $fillable = [
        'channel_name',
        'host_user_id',
        'title',
        'status',
        'started_at',
        'ended_at',
        'cdn_push_url',
        'viewers_count',
    ];
}
