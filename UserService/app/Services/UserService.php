<?php

namespace App\Services;

use App\Contracts\UserServiceInterface;
use App\Exceptions\InvalidLoginCredentialsException;
use App\Models\User;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Throwable;

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
     * @throws GuzzleException
     * @throws InvalidLoginCredentialsException
     */
    public function login(array $data): array
    {
        $user = $this->user->whereEmail($data['email'])->first();

        if (! $user || ! Hash::check($data['password'], $user->password)) {
            throw new InvalidLoginCredentialsException();
        }

        $token = $this->generateToken($data);

        return compact('user', 'token');
    }

    /**
     * @param $data
     * @return mixed
     * @throws GuzzleException
     */
    public function generateToken($data): mixed
    {
        $oauthClientId = config('services.oauth.client_id');
        $oauthClientSecret = config('services.oauth.client_secret');
        $oauthBaseUrl = config('services.oauth.base_url');

        $generateToken = $this->sendRequest('POST', $oauthBaseUrl, [
            'grant_type' => 'password',
            'client_id' => $oauthClientId,
            'client_secret' => $oauthClientSecret,
            'username' => $data['email'],
            'password' => $data['password'],
            'scope' => '*',
        ]);

        return json_decode($generateToken);
    }

    /**
     * @param $method
     * @param $requestUrl
     * @param array $params
     * @param array $headers
     * @return string
     * @throws GuzzleException
     */
    public function sendRequest($method, $requestUrl, array $params = [], array $headers = []): string
    {
        $client = new Client(['base_uri' => $requestUrl]);

        $response = $client->request($method, $requestUrl, [
            'form_params' => $params,
            'headers' => $headers,
        ]);

        return $response->getBody()->getContents();
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
