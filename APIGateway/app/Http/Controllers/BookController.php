<?php

namespace App\Http\Controllers;

use App\Contracts\BookServiceInterface;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Laravel\Lumen\Http\ResponseFactory;

class BookController extends Controller
{
    use ApiResponseTrait;

    public BookServiceInterface $bookService;

    /**
     * @param BookServiceInterface $bookService
     */
    public function __construct(BookServiceInterface $bookService)
    {
        $this->bookService = $bookService;
    }

    /**
     * Return list of books
     *
     * @return Response|ResponseFactory
     */
    public function index(): Response|ResponseFactory
    {
        return $this->success($this->bookService->index());
    }

    /**
     * Create new book
     *
     * @param Request $request
     * @return Response|ResponseFactory
     */
    public function store(Request $request): Response|ResponseFactory
    {
        return $this->success(
            $this->bookService->create($request->all())
        );
    }

    /**
     * get and show details of existing book
     *
     * @param $book
     * @return Response|ResponseFactory
     */
    public function show($book): Response|ResponseFactory
    {
        return $this->success($this->bookService->show($book));
    }

    /**
     * Update an existing book
     *
     * @param Request $request
     * @param $book
     * @return Response|ResponseFactory
     */
    public function update(Request $request, $book): Response|ResponseFactory
    {
        return $this->success($this->bookService->update($request->all(), $book));
    }

    /**
     * Remove an existing book
     *
     * @param $book
     * @return JsonResponse
     */
    public function destroy($book): \Illuminate\Http\JsonResponse
    {
        $this->bookService->delete($book);

        return $this->deleted();
    }
}
