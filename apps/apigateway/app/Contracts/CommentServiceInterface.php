<?php

namespace App\Contracts;

interface CommentServiceInterface
{
    public function index(int $post): string;

    public function store(array $data): string;

    public function show(int $author): string;

    public function update(array $data, int $id): string;

    public function like(array $data, int $id): string;

    public function delete(int $id): void;
}
