<?php

namespace App\Traits;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

trait ConsumeExternalServiceTrait
{
    /**
     * @throws GuzzleException
     */
    public function sendRequest($method, $requestUrl, $params = [], $headers = []): string
    {
        $client = new Client([
            'base_uri' => $this->baseUrl,
        ]);

        $response = $client->request($method, $requestUrl, [
            'form_params' => $params,
            'headers' => $headers
        ]);

        return $response->getBody()->getContents();
    }
}
