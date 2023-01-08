<?php

namespace App\Contracts;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

interface UserServiceInterface
{
    public function index(): Collection;

    public function create(array $data): Model;

    public function show(int $author): Model;

    public function update(array $data, int $id): Model;

    public function delete(int $id): void;
}
