<?php

namespace Tests\Feature;

use Laravel\Lumen\Testing\DatabaseMigrations;
use Tests\TestCase;

class UserControllerTest extends TestCase
{
    use DatabaseMigrations;

    protected function setup(): void
    {
        parent::setUp();
    }

    public function testShouldReturnAllUsers()
    {
        $response = $this->get($this->baseUrl . '/users');
        $data = $this->responseData($response);

        $this->assertCount(10, $data->data);
        $this->assertEquals('success', $data->status);

        $response->assertResponseOk();
        $response->seeJsonStructure([
            'status', 'message',
            'data' => [
                '*' => [
                    'id', 'firstname', 'lastname', 'gender', 'email', 'email_verified_at', 'remember_token', 'created_at', 'updated_at'
                ]
            ],
        ]);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
    }
}
