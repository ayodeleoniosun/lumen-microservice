<?php

namespace App\Contracts;

interface UserServiceInterface
{
    public function index(): string;

    public function register(array $data): string;

    public function login(array $data): string;

    public function show(int $author): string;

    public function update(array $data, int $id): string;
}
