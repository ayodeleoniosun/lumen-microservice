<?php

namespace App\Http\Controllers;

use App\Contracts\AuthServiceInterface;
use App\Http\Requests\CreateUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Laravel\Lumen\Http\ResponseFactory;

class AuthController extends Controller
{
    use ApiResponseTrait;

    public AuthServiceInterface $authService;

    /**
     * @param AuthServiceInterface $authService
     */
    public function __construct(AuthServiceInterface $authService)
    {
        $this->authService = $authService;
    }

    /**
     * Return list of users
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $response = $this->authService->index();

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
        $response = $this->authService->register($request->validated());

        return $this->success($response, 'Registration successful', Response::HTTP_CREATED);
    }

    /**
     * Login user
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function login(Request $request): JsonResponse
    {
        $response = $this->authService->login($request->all());

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
        $response = $this->authService->show($user);

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
        $response = $this->authService->update($request->validated(), $user);

        return $this->success($response, 'Profile successfully updated');
    }
}
