<?php

namespace App\Services;

use App\Contracts\UserServiceInterface;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;

class UserService implements UserServiceInterface
{
    public User $user;

    /**
     * @param User $user
     */
    public function __construct(User $user)
    {
        $this->user = $user;
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
     * @return Model
     */
    public function create(array $data): Model
    {
        $data['password'] = Hash::make($data['password']);
        $data['email_verified_at'] = Carbon::now()->toDateTimeString();

        return $this->user->create($data);
    }

    /**
     *  Show user details
     * @return Model
     */
    public function show(int $user): Model
    {
        return $this->user->findOrFail($user);
    }

    /**
     *  Update user details
     * @return Model
     */
    public function update(array $data, int $id): Model
    {
        $user = $this->show($id);

        $user->firstname = $data['firstname'];
        $user->lastname = $data['lastname'];
        $user->gender = $data['gender'];

        $user->save();

        return $user;
    }

    /**
     *  Remove user details
     * @return void
     */
    public function delete(int $id): void
    {
        $user = $this->show($id);
        $user->delete();
    }
}
