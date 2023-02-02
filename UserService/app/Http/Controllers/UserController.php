<?php

namespace App\Http\Controllers;

use App\Contracts\UserServiceInterface;
use App\Http\Requests\CreateUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class UserController extends Controller
{
    use ApiResponseTrait;

    protected UserServiceInterface $userService;

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
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $response = $this->userService->index();

        return $this->success($response);
    }

    /**
     * Register new user
     *
     * @param CreateUserRequest $request
     * @return JsonResponse
     */
    public function register(CreateUserRequest $request): JsonResponse
    {
        $response = $this->userService->register($request->validated());

        return $this->success($response, 'Registration successful', Response::HTTP_CREATED);
    }

    /**
     * Register new user
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function login(Request $request): JsonResponse
    {
        $response = $this->userService->login($request->all());

        return $this->success($response, 'Login successful');
    }

    /**
     * get and show details of existing user
     *
     * @param $user
     * @return JsonResponse
     */
    public function show($user): JsonResponse
    {
        $response = $this->userService->show($user);

        return $this->success($response);
    }

    /**
     * Update an existing user
     *
     * @param UpdateUserRequest $request
     * @param $user
     * @return JsonResponse
     */
    public function update(UpdateUserRequest $request, $user): JsonResponse
    {
        $response = $this->userService->update($request->validated(), $user);

        return $this->success($response, 'Profile successfully updated');
    }
}
