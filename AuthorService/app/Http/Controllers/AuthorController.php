<?php

namespace App\Http\Controllers;

use App\Contracts\AuthorServiceInterface;
use App\Http\Requests\CreateAuthorRequest;
use App\Http\Requests\UpdateAuthorRequest;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class AuthorController extends Controller
{
    use ApiResponseTrait;

    protected AuthorServiceInterface $authorService;

    /**
     * @param AuthorServiceInterface $authorService
     */
    public function __construct(AuthorServiceInterface $authorService)
    {
        $this->authorService = $authorService;
    }

   /**
     * Return list of authors
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $response = $this->authorService->index();

        return $this->success($response);
    }

    /**
     * Create new author
     *
     * @return JsonResponse
     */
    public function store(CreateAuthorRequest $request): JsonResponse
    {
        $response = $this->authorService->create($request->validated());

        return $this->success($response, 'Author successfully added', Response::HTTP_CREATED);
    }

    /**
     * get and show details of existing author
     *
     * @return JsonResponse
     */
    public function show($author): JsonResponse
    {
        $response = $this->authorService->show($author);

        return $this->success($response);
    }

    /**
     * Update an existing author
     *
     * @return JsonResponse
     */
    public function update(UpdateAuthorRequest $request, $author): JsonResponse
    {
        $response = $this->authorService->update($request->validated(), $author);

        return $this->success($response, 'Author successfully updated');
    }

    /**
     * Remove an existing author
     *
     * @return JsonResponse
     */
    public function destroy($author): JsonResponse
    {
        $this->authorService->delete($author);

        return $this->deleted();
    }
}
