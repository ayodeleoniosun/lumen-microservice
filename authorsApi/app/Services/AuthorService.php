<?php

namespace App\Services;

use App\Contracts\AuthorServiceInterface;
use App\Models\Author;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class AuthorService implements AuthorServiceInterface
{
    public function authors(): Collection
    {
        return Author::all();
    }

    public function create(array $data): Model
    {
        return Author::create($data);
    }
}
