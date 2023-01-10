<?php

namespace App\Contracts;

use App\Http\Resources\PostCollection;
use App\Http\Resources\PostResource;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

interface PostServiceInterface
{
    public function index(): PostCollection;

    public function create(array $data): Model;

    public function show(int $post): PostResource;

    public function update(array $data, int $post): Model;

    public function like(int $user, int $id): Model;

    public function delete(int $post): void;
}
