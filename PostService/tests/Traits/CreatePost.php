<?php

namespace Tests\Traits;

use App\Models\Post\Post;

trait CreatePost
{
    protected function createPost($count = 10)
    {
        return Post::factory()->count($count)->create();
    }
}
