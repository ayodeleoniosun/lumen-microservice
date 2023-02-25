<?php

namespace App\Http\Middleware;

use App\Traits\ApiResponseTrait;
use Closure;
use Illuminate\Contracts\Auth\Factory as Auth;
use Illuminate\Http\Request;

class ApiToken
{
    use ApiResponseTrait;

    /**
     * The authentication guard factory instance.
     *
     * @var Auth
     */
    protected Auth $auth;

    /**
     * Create a new middleware instance.
     *
     * @param Auth $auth
     * @return void
     */
    public function __construct(Auth $auth)
    {
        $this->auth = $auth;
    }

    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (!$request->header('x-api-key') || !config('services.api_token.secret')) {
            return $this->error('Unauthenticated.', 401);
        }

        if ($request->header('x-api-key') !== config('services.api_token.secret')) {
            return $this->error('Unauthenticated.', 401);
        }

        return $next($request);
    }
}
