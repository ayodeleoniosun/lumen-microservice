<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CommentLike extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public function comment(): BelongsTo
    {
        return $this->belongsTo(Comment::class);
    }
}
