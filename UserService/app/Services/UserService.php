<?php

namespace App\Services;

use App\Contracts\OauthServiceInterface;
use App\Contracts\UserServiceInterface;
use App\Exceptions\InvalidLoginException;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Throwable;

class UserService implements UserServiceInterface
{
    public User $user;

    public OauthServiceInterface $oauthService;

    /**
     * @param User $user
     * @param OauthServiceInterface $oauthService
     */
    public function __construct(User $user, OauthServiceInterface $oauthService)
    {
        $this->user = $user;
        $this->oauthService = $oauthService;
    }

    /**
     * Get all users
     *
     * @return Collection
     */
    public function index(): Collection
    {
        return $this->user->all();
    }

    /**
     * Create new user
     *
     * @param array $data
     * @return Model
     */
    public function register(array $data): Model
    {
        $data['password'] = Hash::make($data['password']);
        $data['email_verified_at'] = Carbon::now()->toDateTimeString();

        return $this->user->create($data);
    }

    /**
     * Create new user
     *
     * @param array $data
     * @return array
     * @throws Throwable
     */
    public function login(array $data): array
    {
        $user = $this->user->whereEmail($data['email'])->first();

        throw_if((! $user || ! Hash::check($data['password'], $user->password)), InvalidLoginException::class);

        $token = $this->oauthService->generateToken($data);

        return compact('user', 'token');
    }

    /**
     *  Show user details
     * @param int $user
     * @return Model
     */
    public function show(int $user): Model
    {
        return $this->user->findOrFail($user);
    }

    /**
     *  Update user details
     * @param array $data
     * @param int $id
     * @return Model
     * @throws Throwable
     */
    public function update(array $data, int $id): Model
    {
        throw_if(auth()->user()->id !== $id, AuthorizationException::class);

        $user = $this->show($id);

        $user->firstname = $data['firstname'];
        $user->lastname = $data['lastname'];
        $user->gender = $data['gender'];

        $user->save();

        return $user;
    }
}
