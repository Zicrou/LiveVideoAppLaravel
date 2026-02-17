<?php

namespace App\Models;

use App\Models\Concerns\HasUuid;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasUuid;
    protected $fillable = [
        'post_type',
        'owner_id',
    ];

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    protected $casts = [
        'post_type' => 'string',
        'owner_id' => 'string',
    ];
}
