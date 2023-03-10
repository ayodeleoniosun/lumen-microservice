<?php

namespace Tests\Traits;

use App\Models\Comment;

trait CreateComment
{
    protected function createComment($count = 10)
    {
        return Comment::factory()->count($count)->create();
    }
}
