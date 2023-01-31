<?php

namespace App\Contracts;

use App\Http\Resources\Comment\CommentCollection;
use App\Http\Resources\Comment\CommentResource;
use Illuminate\Database\Eloquent\Model;

interface CommentServiceInterface
{
    public function index(int $post): CommentCollection;

    public function create(array $data): Model;

    public function show(int $comment): CommentResource;

    public function update(array $data, int $id): Model;

    public function like(int $user, int $id): Model;

    public function delete(int $id): void;
}
