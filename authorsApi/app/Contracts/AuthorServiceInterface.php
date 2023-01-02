<?php

namespace App\Contracts;

use Illuminate\Database\Eloquent\Collection;

interface AuthorServiceInterface
{
    public function authors(): Collection;
}
