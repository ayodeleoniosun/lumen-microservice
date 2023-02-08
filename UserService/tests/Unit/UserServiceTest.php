<?php

namespace Tests\Unit;

use App\Contracts\OauthServiceInterface;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Laravel\Passport\Passport;
use Tests\TestCase;
use Tests\Traits\CreateUser;

class UserServiceTest extends TestCase
{
    use CreateUser;
    use DatabaseMigrations;

    public UserService $userService;
    public User $user;

    protected function setup(): void
    {
        parent::setUp();
        $this->user = new User();
        $this->oauthService = \Mockery::mock(OauthServiceInterface::class)->makePartial();
        $this->userService = new UserService($this->user, $this->oauthService);

        Passport::actingAs(
            User::factory()->create(),
            ['*']
        );
    }

    public function testCanReturnAllUsers()
    {
        $this->createUser(9);
        $response = $this->userService->index();

        $this->assertCount(10, $response);
        $this->assertInstanceOf(Collection::class, $response);
    }

    public function testCanCreateNewUser()
    {
        $payload = [
            'firstname' => 'John',
            'lastname' => 'Doe',
            'gender' => 'male',
            'email' => 'johndoe@gmail.com',
            'password' => 'strong_password'
        ];

        $response = $this->userService->register($payload);

        $this->assertInstanceOf(User::class, $response);
        $this->assertEquals($payload['firstname'], $response->firstname);
        $this->assertEquals($payload['lastname'], $response->lastname);
        $this->assertEquals($payload['gender'], $response->gender);
        $this->assertEquals($payload['email'], $response->email);
    }

    public function testCannotShowInvalidUserDetails()
    {
        $this->expectException(ModelNotFoundException::class);
        $this->userService->show(2);
    }

    public function testCanShowUserDetails()
    {
        $user = $this->createNewUser();
        $response = $this->userService->show($user->id);

        $this->assertInstanceOf(User::class, $response);

        $this->assertInstanceOf(User::class, $response);
        $this->assertEquals($user->id, $response->id);
        $this->assertEquals($user->firstname, $response->firstname);
        $this->assertEquals($user->lastname, $response->lastname);
        $this->assertEquals($user->gender, $response->gender);
        $this->assertEquals($user->email, $response->email);
    }

    public function testCannotUpdateUnAuthorizedUser()
    {
        $payload = $this->updateUserPayload();

        $this->expectException(AuthorizationException::class);
        $this->userService->update($payload, 3);
    }


    public function testCanUpdateExistingUser()
    {
        $payload = $this->updateUserPayload();
        $response = $this->userService->update($payload, auth()->user()->id);

        $this->assertInstanceOf(User::class, $response);
        $this->assertEquals($payload['firstname'], $response->firstname);
        $this->assertEquals($payload['lastname'], $response->lastname);
        $this->assertEquals($payload['gender'], $response->gender);
    }

    private function newUserPayload(): array
    {
        return [
            'firstname' => 'John',
            'lastname' => 'Doe',
            'gender' => 'male',
            'email' => 'johndoe@gmail.com',
            'password' => 'strong_password'
        ];
    }

    private function updateUserPayload(): array
    {
        return [
            'firstname' => 'Updated John',
            'lastname' => 'Updated Doe',
            'gender' => 'female',
        ];
    }

    private function createNewUser(): Model
    {
        $payload = $this->newUserPayload();

        return $this->userService->register($payload);
    }

    public function tearDown():void
    {
        \Mockery::close();
        parent::tearDown();
    }
}
