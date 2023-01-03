<?php

namespace App\Services;

use App\Contracts\AuthorServiceInterface;
use App\Models\Author;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class AuthorService implements AuthorServiceInterface
{
    /**
     * Get all authors
     *
     * @return Collection
     */
    public function index(): Collection
    {
        return Author::all();
    }

    /**
     * Create new author
     *
     * @return Model
     */
    public function create(array $data): Model
    {
        return Author::create($data);
    }

    /**
     *  Show author details
     * @return Model
     */
    public function show(int $author): Model
    {
        return Author::findOrFail($author);
    }

    /**
     *  Update author details
     * @return Model
     */
    public function update(array $data, int $id): Model
    {
        $author = Author::findOrFail($id);

        $author->firstname = $data['firstname'];
        $author->lastname = $data['lastname'];
        $author->gender = $data['gender'];
        $author->country = $data['country'];
        $author->save();

        return $author;
    }

    /**
     *  Remove author details
     * @return void
     */
    public function delete(int $id): void
    {
        $author = Author::findOrFail($id);
        $author->delete();
    }
}
