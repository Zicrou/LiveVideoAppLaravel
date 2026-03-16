<?php

namespace App\Models;

use App\Models\Concerns\HasUuid;
use Illuminate\Database\Eloquent\Model;

class CommentLike extends Model
{
    use HasUuid;
    protected $fillable = [
        'user_id',
        'comment_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }   

    public function comment()
    {
        return $this->belongsTo(CommentLike::class);
    }
}
