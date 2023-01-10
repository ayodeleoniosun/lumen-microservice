<?php

namespace App\Http\Controllers;

use App\Contracts\BookServiceInterface;
use App\Http\Requests\BookRequest;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class BookController extends Controller
{
    use ApiResponseTrait;

    protected BookServiceInterface $bookService;

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
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $response = $this->bookService->index();

        return $this->success($response);
    }

    /**
     * Create new book
     *
     * @return JsonResponse
     */
    public function store(BookRequest $request): JsonResponse
    {
        $response = $this->bookService->create($request->validated());

        return $this->success($response, 'Book successfully added', Response::HTTP_CREATED);
    }

    /**
     * get and show details of existing book
     *
     * @return JsonResponse
     */
    public function show($book): JsonResponse
    {
        $response = $this->bookService->show($book);

        return $this->success($response);
    }

    /**
     * Update an existing book
     *
     * @return JsonResponse
     */
    public function update(BookRequest $request, $book): JsonResponse
    {
        $response = $this->bookService->update($request->validated(), $book);

        return $this->success($response, 'Book successfully updated');
    }

    /**
     * Remove an existing book
     *
     * @return JsonResponse
     */
    public function destroy($book): JsonResponse
    {
        $this->bookService->delete($book);

        return $this->deleted();
    }
}
