<?php

namespace Tests\Feature;

use Illuminate\Http\Response;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Tests\TestCase;
use Tests\Traits\CreateUser;

class UserControllerTest extends TestCase
{
    use CreateUser;
    use DatabaseMigrations;

    public function testShouldReturnAllUsers()
    {
        $this->createUser();
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

    public function testIncompletePayloadShouldNotCreateNewUser()
    {
        $response = $this->post($this->baseUrl . '/users', [
            'firstname' => 'ayodele',
            'lastname' => 'oniosun'
        ]);

        $data = $this->responseData($response);

        $this->assertEquals('error', $data->status);
        $this->assertResponseStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function testInvalidGenderShouldNotCreateNewUser()
    {
        $response = $this->post($this->baseUrl . '/users', [
            'firstname' => 'ayodele',
            'lastname' => 'oniosun',
            'gender' => 'anything'
        ]);

        $data = $this->responseData($response);

        $this->assertEquals('error', $data->status);
        $this->assertResponseStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->assertStringContainsString('The selected gender is invalid', $data->message);
    }

    public function testInvalidEmailShouldNotCreateNewUser()
    {
        $response = $this->post($this->baseUrl . '/users', [
            'firstname' => 'ayodele',
            'lastname' => 'oniosun',
            'gender' => 'male',
            'email' => 'anything'
        ]);

        $data = $this->responseData($response);

        $this->assertEquals('error', $data->status);
        $this->assertResponseStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->assertStringContainsString('The email must be a valid email address', $data->message);
    }

    public function testExistingEmailShouldNotCreateNewUser()
    {
        $payload = [
            'firstname' => 'ayodele',
            'lastname' => 'oniosun',
            'gender' => 'male',
            'email' => 'anything@gmail.com',
            'password' => 'password'
        ];

        $this->post($this->baseUrl . '/users', $payload);
        $response = $this->post($this->baseUrl . '/users', $payload);
        $data = $this->responseData($response);

        $this->assertEquals('error', $data->status);
        $this->assertResponseStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->assertEquals('The email has already been taken.', $data->message);
    }

    public function testShouldCreateNewUser()
    {
        $data = $this->createNewUserAndReturnData();

        $this->assertEquals('success', $data->status);
        $this->assertEquals('Registration successful', $data->message);
        $this->assertResponseStatus(Response::HTTP_CREATED);
    }

    public function testUserNotFound()
    {
        $response = $this->get($this->baseUrl . "/users/1");
        $data = $this->responseData($response);

        $this->assertEquals('error', $data->status);
        $this->assertEquals('User not found', $data->message);
        $this->assertResponseStatus(Response::HTTP_NOT_FOUND);
    }

    public function testShouldShowUserDetails()
    {
        $data = $this->createNewUserAndReturnData();
        $response = $this->get($this->baseUrl . "/users/{$data->data->id}");
        $userResponse = $this->responseData($response);

        $this->assertEquals('success', $userResponse->status);
        $this->assertEquals($data->data->id, $userResponse->data->id);
        $this->assertResponseStatus(Response::HTTP_OK);
    }

    public function testShouldNotUpdateInvalidUser()
    {
        $response = $this->put($this->baseUrl . "/users/1", [
            'firstname' => 'ayodele new',
            'lastname' => 'oniosun new',
            'gender' => 'female',
        ]);

        $userResponse = $this->responseData($response);

        $this->assertEquals('error', $userResponse->status);
        $this->assertEquals('User not found', $userResponse->message);
        $this->assertResponseStatus(Response::HTTP_NOT_FOUND);
    }

    public function testShouldUpdateUser()
    {
        $data = $this->createNewUserAndReturnData();

        $response = $this->put($this->baseUrl . "/users/{$data->data->id}", [
            'firstname' => 'ayodele new',
            'lastname' => 'oniosun new',
            'gender' => 'female',
        ]);

        $userResponse = $this->responseData($response);

        $this->assertEquals('success', $userResponse->status);
        $this->assertEquals('Profile successfully updated', $userResponse->message);
        $this->assertEquals($data->data->id, $userResponse->data->id);
        $this->assertEquals('ayodele new', $userResponse->data->firstname);
        $this->assertEquals('oniosun new', $userResponse->data->lastname);
        $this->assertResponseStatus(Response::HTTP_OK);
    }

    public function testShouldNotDeleteInvalidUser()
    {
        $response = $this->delete($this->baseUrl . "/users/1");
        $userResponse = $this->responseData($response);

        $this->assertEquals('error', $userResponse->status);
        $this->assertEquals('User not found', $userResponse->message);
        $this->assertResponseStatus(Response::HTTP_NOT_FOUND);
    }

    public function testShouldDeleteUser()
    {
        $data = $this->createNewUserAndReturnData();
        $this->delete($this->baseUrl . "/users/{$data->data->id}");

        $this->assertResponseStatus(Response::HTTP_NO_CONTENT);
    }

    private function createNewUserAndReturnData()
    {
        $response = $this->post($this->baseUrl . '/users', [
            'firstname' => 'ayodele',
            'lastname' => 'oniosun',
            'gender' => 'male',
            'email' => 'anything@gmail.com',
            'password' => 'password'
        ]);

        return $this->responseData($response);
    }
}
