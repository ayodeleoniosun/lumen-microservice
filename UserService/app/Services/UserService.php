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
    /**
     * Get all users
     *
     * @return Collection
     */
    public function index(): Collection
    {
        return User::all();
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
        return User::create($data);
    }

    /**
     *  Show user details
     * @return Model
     */
    public function show(int $user): Model
    {
        return User::findOrFail($user);
    }

    /**
     *  Update user details
     * @return Model
     */
    public function update(array $data, int $id): Model
    {
        $user = User::findOrFail($id);

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
        $user = User::findOrFail($id);
        $user->delete();
    }
}
