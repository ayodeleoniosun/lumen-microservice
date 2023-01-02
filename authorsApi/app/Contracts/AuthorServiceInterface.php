<?php

namespace App\Contracts;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

interface AuthorServiceInterface
{
    public function authors(): Collection;

    public function create(array $data): Model;

    public function show(int $author): Model;
}
