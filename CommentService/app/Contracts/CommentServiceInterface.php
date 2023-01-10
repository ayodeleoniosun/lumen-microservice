<?php

namespace App\Contracts;

use App\Http\Resources\CommentCollection;
use App\Http\Resources\CommentResource;
use Illuminate\Database\Eloquent\Model;

interface CommentServiceInterface
{
    public function index(): CommentCollection;

    public function create(array $data): Model;

    public function show(int $comment): CommentResource;

    public function update(array $data, int $id): Model;

    public function like(int $user, int $id): Model;

    public function delete(int $id): void;
}
