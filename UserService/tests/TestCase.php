<?php

namespace Tests;

use Laravel\Lumen\Application;
use Laravel\Lumen\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected $baseUrl;

    /**
     * Creates the application.
     *
     * @return Application
     */
    public function createApplication()
    {
        return require __DIR__.'/../bootstrap/app.php';
    }

    protected function setup(): void
    {
        parent::setUp();
        $this->baseUrl = config('app.url');
    }

    protected function responseData($response, $key = 'all')
    {
        if ($key == 'all') {
            return json_decode($response->response->content());
        }

        return json_decode($response->response->content())->$key;
    }
}
