<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Http\Response;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Passport\Passport;
use Tests\TestCase;
use Tests\Traits\CreateUser;

class AuthControllerTest extends TestCase
{
    use CreateUser;
    use DatabaseMigrations;

    public function testShouldReturnAllUsers()
    {
        $this->createUser(9);
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
        $response = $this->post($this->baseUrl . '/register', [
            'firstname' => 'ayodele',
            'lastname' => 'oniosun'
        ]);

        $data = $this->responseData($response);

        $this->assertEquals('error', $data->status);
        $this->assertStringContainsString('The gender field is required.', $data->message);
        $this->assertResponseStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function testInvalidGenderShouldNotCreateNewUser()
    {
        $response = $this->post($this->baseUrl . '/register', [
            'firstname' => 'ayodele',
            'lastname' => 'oniosun',
            'gender' => 'anything'
        ]);

        $data = $this->responseData($response);

        $this->assertEquals('error', $data->status);
        $this->assertStringContainsString('The selected gender is invalid', $data->message);
        $this->assertResponseStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function testInvalidEmailShouldNotCreateNewUser()
    {
        $response = $this->post($this->baseUrl . '/register', [
            'firstname' => 'ayodele',
            'lastname' => 'oniosun',
            'gender' => 'male',
            'email' => 'anything'
        ]);

        $data = $this->responseData($response);

        $this->assertEquals('error', $data->status);
        $this->assertStringContainsString('The email must be a valid email address', $data->message);
        $this->assertResponseStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function testExistingEmailShouldNotCreateNewUser()
    {
        $this->post($this->baseUrl . '/register', [
            'firstname' => 'ayodele',
            'lastname' => 'oniosun',
            'gender' => 'male',
            'email' => 'anything@gmail.com',
            'password' => 'password'
        ]);

        $data = $this->createNewUserAndReturnData();

        $this->assertEquals('error', $data->status);
        $this->assertEquals('The email has already been taken.', $data->message);
        $this->assertResponseStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    private function createNewUserAndReturnData()
    {
        $response = $this->post($this->baseUrl . '/register', [
            'firstname' => 'ayodele',
            'lastname' => 'oniosun',
            'gender' => 'male',
            'email' => 'anything@gmail.com',
            'password' => '12345'
        ]);

        return $this->responseData($response);
    }

    public function testShouldCreateNewUser()
    {
        $data = $this->createNewUserAndReturnData();

        $this->assertEquals('success', $data->status);
        $this->assertEquals('Registration successful', $data->message);
        $this->assertResponseStatus(Response::HTTP_CREATED);
    }

//    public function testShouldLoginValidUser()
//    {
//        $data = $this->createNewUserAndReturnData();
//
//        $response = $this->post($this->baseUrl . '/login', [
//            'email' => $data->data->email,
//            'password' => '12345'
//        ]);
//
//        $login = $this->responseData($response);
//
//        $this->assertEquals('success', $login->status);
//        $this->assertEquals('Login successful', $login->message);
//        $this->assertResponseStatus(Response::HTTP_OK);
//    }

    public function testUserNotFound()
    {
        $response = $this->get($this->baseUrl . "/users/100");

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

    public function testUnAuthorizedToUpdateUser()
    {
        $data = $this->updateUserAndReturnData(100);

        $this->assertEquals('error', $data->status);
        $this->assertEquals('This action is unauthorized.', $data->message);
        $this->assertResponseStatus(Response::HTTP_FORBIDDEN);
    }

    private function updateUserAndReturnData(int $id)
    {
        $response = $this->put($this->baseUrl . "/users/{$id}", [
            'firstname' => 'updated firstname',
            'lastname' => 'updated lastname',
            'gender' => 'female',
        ]);

        return $this->responseData($response);
    }

    public function testShouldUpdateUser()
    {
        $userResponse = $this->updateUserAndReturnData(1);

        $this->assertEquals('success', $userResponse->status);
        $this->assertEquals('Profile successfully updated', $userResponse->message);
        $this->assertEquals(1, $userResponse->data->id);
        $this->assertEquals('updated firstname', $userResponse->data->firstname);
        $this->assertEquals('updated lastname', $userResponse->data->lastname);
        $this->assertEquals('female', $userResponse->data->gender);
        $this->assertResponseStatus(Response::HTTP_OK);
    }

    protected function setup(): void
    {
        parent::setUp();

        Passport::actingAs(
            User::factory()->create(),
            ['*']
        );
    }

    protected function tearDown(): void
    {
        parent::tearDown();
    }

}
