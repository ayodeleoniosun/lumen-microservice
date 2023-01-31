<?php

namespace App\Contracts;

use App\Http\Resources\Post\PostCollection;
use App\Http\Resources\Post\PostResource;
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
