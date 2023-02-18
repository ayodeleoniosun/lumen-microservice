<?php

namespace Tests\Traits;

use App\Models\User;

trait CreateUser
{
    protected function createUser($count = 10)
    {
        return User::factory()->count($count)->create();
    }
}
