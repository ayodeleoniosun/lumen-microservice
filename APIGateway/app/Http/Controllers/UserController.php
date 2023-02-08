<?php

namespace App\Http\Controllers;

use App\Contracts\UserServiceInterface;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Laravel\Lumen\Http\ResponseFactory;

class UserController extends Controller
{
    use ApiResponseTrait;

    public UserServiceInterface $userService;

    /**
     * @param UserServiceInterface $userService
     */
    public function __construct(UserServiceInterface $userService)
    {
        $this->userService = $userService;
    }

    /**
     * Return list of users
     *
     * @return Response|ResponseFactory
     */
    public function index(): Response|ResponseFactory
    {
        return $this->success($this->userService->index());
    }

    /**
     * Register new user
     *
     * @param Request $request
     * @return Response|ResponseFactory
     */
    public function register(Request $request): Response|ResponseFactory
    {
        return $this->success(
            $this->userService->register($request->all())
        );
    }

    /**
     * Login user
     *
     * @param Request $request
     * @return Response|ResponseFactory
     */
    public function login(Request $request): Response|ResponseFactory
    {
        return $this->success(
            $this->userService->login($request->all())
        );
    }

    /**
     * get and show details of existing user
     *
     * @param $user
     * @return Response|ResponseFactory
     */
    public function show($user): Response|ResponseFactory
    {
        return $this->success($this->userService->show($user));
    }

    /**
     * Update an existing user
     *
     * @param Request $request
     * @param $user
     * @return Response|ResponseFactory
     */
    public function update(Request $request, $user): Response|ResponseFactory
    {
        return $this->success($this->userService->update($request->all(), $user));
    }
}
