<?php

namespace App\Contracts;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

interface UserServiceInterface
{
    public function index(): Collection;

    public function register(array $data): Model;

    public function login(array $data): array;

    public function show(int $user): Model;

    public function update(array $data, int $id): Model;
}
