<?php

namespace App\Services;

use App\Contracts\AuthorServiceInterface;
use App\Exceptions\CustomException;
use App\Models\Author;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Response;

class AuthorService implements AuthorServiceInterface
{
    /**
     * Get all authors
     *
     * @return Collection
     */
    public function authors(): Collection
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
     * @throws CustomException
     * @return Model
     */
    public function show(int $author): Model
    {
        try {
            return Author::findOrFail($author);
        } catch (\Exception $e) {
            if ($e instanceof  ModelNotFoundException) {
                throw new CustomException('Author not found', Response::HTTP_NOT_FOUND);
            }
        }
    }

    /**
     *  Show author details
     * @throws CustomException
     * @return void
     */
    public function update(array $data, int $id): Model
    {
        try {
            $author = Author::findOrFail($id);

            $author->firstname = $data['firstname'];
            $author->lastname = $data['lastname'];
            $author->gender = $data['gender'];
            $author->country = $data['country'];
            $author->save();

            return $author;
        } catch (\Exception $e) {
            if ($e instanceof  ModelNotFoundException) {
                throw new CustomException('Author not found', Response::HTTP_NOT_FOUND);
            }
        }
    }
}
