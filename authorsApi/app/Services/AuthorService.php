<?php

namespace App\Services;

use App\Contracts\AuthorServiceInterface;
use App\Models\Author;
use Illuminate\Database\Eloquent\Collection;

class AuthorService implements AuthorServiceInterface
{
    public function authors(): Collection
    {
        return Author::all();
    }
}
