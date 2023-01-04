<?php

namespace App\Contracts;

interface AuthorServiceInterface
{
    public function index(): string;

    public function create(array $data): string;

    public function show(int $author): string;

    public function update(array $data, int $id): string;

    public function delete(int $id): void;
}
