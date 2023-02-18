<?php

namespace App\Services;

use App\Contracts\OauthServiceInterface;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class OauthService implements OauthServiceInterface
{
    public string $clientId;
    public string $clientSecret;
    public string $baseUrl;

    /**
     *
     */
    public function __construct()
    {
        $this->clientId = config('services.oauth.client_id');
        $this->clientSecret = config('services.oauth.client_secret');
        $this->baseUrl = config('services.oauth.base_url');
    }

    /**
     * @param $data
     * @return mixed
     * @throws GuzzleException
     */
    public function generateToken($data): mixed
    {
        $generateToken = $this->sendRequest('POST', $this->baseUrl, [
            'grant_type' => 'password',
            'client_id' => $this->clientId,
            'client_secret' => $this->clientSecret,
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
}
