<?php

namespace App\Services;

use App\Contracts\BookServiceInterface;
use App\Exceptions\BookExistException;
use App\Models\Book;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use function PHPUnit\Framework\throwException;

class BookService implements BookServiceInterface
{
    /**
     * Get all books
     *
     * @return Collection
     */
    public function index(): Collection
    {
        return Book::all();
    }

    /**
     * Create new book
     *
     * @return Model
     * @throws BookExistException
     */
    public function create(array $data): Model
    {
        $bookExist = Book::where([
            'author_id' => $data['author_id'],
            'title' => $data['title']
        ])->exists();

        if ($bookExist) throw new BookExistException();

        return Book::create($data);
    }

    /**
     *  Show book details
     * @return Model
     */
    public function show(int $book): Model
    {
        return Book::findOrFail($book);
    }

    /**
     *  Update book details
     * @return Model
     * @throws BookExistException
     */
    public function update(array $data, int $id): Model
    {
        $book = Book::findOrFail($id);

        $bookExist = Book::where([
            'author_id' => $data['author_id'],
            'title' => $data['title']
        ])->whereNot('id', $id)->exists();

        if ($bookExist) throw new BookExistException();

        $book->author_id = $data['author_id'];
        $book->title = $data['title'];
        $book->description = $data['description'];
        $book->pages = $data['pages'];
        $book->isbn = $data['isbn'];
        $book->price = $data['price'];
        $book->save();

        return $book;
    }

    /**
     *  Remove book details
     * @return void
     */
    public function delete(int $id): void
    {
        $book = Book::findOrFail($id);
        $book->delete();
    }
}
