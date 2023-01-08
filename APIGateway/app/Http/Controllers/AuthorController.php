<?php

namespace App\Http\Controllers;

use App\Contracts\AuthorServiceInterface;
use App\Contracts\UserServiceInterface;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Laravel\Lumen\Http\ResponseFactory;

class AuthorController extends Controller
{
    use ApiResponseTrait;

    public AuthorServiceInterface $authorService;

    public UserServiceInterface $userService;

    /**
     * @param AuthorServiceInterface $authorService
     * @param UserServiceInterface $userService
     */
    public function __construct(AuthorServiceInterface $authorService, UserServiceInterface $userService)
    {
        $this->authorService = $authorService;
        $this->userService = $userService;
    }

    /**
     * Return list of authors
     *
     * @return Response|ResponseFactory
     */
    public function index(): Response|ResponseFactory
    {
        return $this->success($this->authorService->index());
    }

    /**
     * Create new author
     *
     * @param Request $request
     * @return Response|ResponseFactory
     */
    public function store(Request $request): Response|ResponseFactory
    {
        $this->userService->show($request->user_id);
        
        return $this->success(
            $this->authorService->create($request->all())
        );
    }

    /**
     * get and show details of existing author
     *
     * @param $author
     * @return Response|ResponseFactory
     */
    public function show($author): Response|ResponseFactory
    {
        return $this->success($this->authorService->show($author));
    }

    /**
     * Update an existing author
     *
     * @param Request $request
     * @param $author
     * @return Response|ResponseFactory
     */
    public function update(Request $request, $author): Response|ResponseFactory
    {
        return $this->success($this->authorService->update($request->all(), $author));
    }

    /**
     * Remove an existing author
     *
     * @param $author
     * @return JsonResponse
     */
    public function destroy($author): JsonResponse
    {
        $this->authorService->delete($author);

        return $this->deleted();
    }
}
