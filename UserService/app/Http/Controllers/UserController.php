<?php

namespace App\Http\Controllers;

use App\Contracts\UserServiceInterface;
use App\Http\Requests\CreateUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;
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
     * Create new user
     *
     * @return JsonResponse
     */
    public function store(CreateUserRequest $request): JsonResponse
    {
        $response = $this->userService->create($request->validated());

        return $this->success($response, 'Registration successful', Response::HTTP_CREATED);
    }

    /**
     * get and show details of existing user
     *
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
     * @return JsonResponse
     */
    public function update(UpdateUserRequest $request, $user): JsonResponse
    {
        $response = $this->userService->update($request->validated(), $user);

        return $this->success($response, 'Profile successfully updated');
    }

    /**
     * Remove an existing user
     *
     * @return JsonResponse
     */
    public function destroy($user): JsonResponse
    {
        $this->userService->delete($user);

        return $this->deleted();
    }
}
