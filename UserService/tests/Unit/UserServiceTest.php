<?php

namespace Tests\Unit;

use App\Models\User;
use App\Services\UserService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Hash;
use Laravel\Lumen\Testing\DatabaseMigrations;
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
        $this->user = \Mockery::mock(User::class)->makePartial();
        $this->userService = new UserService($this->user);
    }

    public function testCanReturnAllUsers()
    {
        $users = new Collection([$this->user]);
        $this->user->shouldReceive('all')->once()->andReturn($users);

        $response = $this->userService->index();
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

        $mockedUser = $this->mockUser();

        Hash::shouldReceive('make')->with($payload['password'])->once()->andReturn('hashed_password');

        $this->user->shouldReceive('create')
            ->once()
            ->andReturn($mockedUser);

        $response = $this->userService->create($payload);

        $this->assertInstanceOf(User::class, $response);
        $this->assertEquals($mockedUser->id, $response->id);
        $this->assertEquals($mockedUser->firstname, $response->firstname);
        $this->assertEquals($mockedUser->lastname, $response->lastname);
        $this->assertEquals($mockedUser->gender, $response->gender);
        $this->assertEquals($mockedUser->email, $response->email);
    }

    public function testCannotShowInvalidUserDetails()
    {
        $this->user->shouldReceive('findOrFail')
            ->once()
            ->with(1)
            ->andThrows(ModelNotFoundException::class, 'User not found');

        $this->expectException(ModelNotFoundException::class);
        $this->expectExceptionMessage('User not found');

        $this->userService->show(1);
    }

    public function testCanShowUserDetails()
    {
        $mockedUser = $this->mockUser();

        $this->user->shouldReceive('findOrFail')
            ->once()
            ->with($mockedUser->id)
            ->andReturn($mockedUser);

        $response = $this->userService->show($mockedUser->id);

        $this->assertInstanceOf(User::class, $response);
        $this->assertEquals($mockedUser->id, $response->id);
        $this->assertEquals($mockedUser->firstname, $response->firstname);
        $this->assertEquals($mockedUser->lastname, $response->lastname);
        $this->assertEquals($mockedUser->gender, $response->gender);
        $this->assertEquals($mockedUser->email, $response->email);
    }

    public function testCanUpdateExistingUser()
    {
        $mockedUser = $this->mockUser();

        $this->user->shouldReceive('findOrFail')
            ->once()
            ->with($mockedUser->id)
            ->andReturn($mockedUser);

        $payload = [
            'firstname' => 'New John',
            'lastname' => 'New Doe',
            'gender' => 'female',
        ];

        $mockUpdateUser = $this->mockUser($payload);

//        $this->app->instance(
//            User::class,
//            \Mockery::mock(User::class, function ($mock) use ($mockUpdateUser) {
//                $mock->shouldReceive('delete')->andReturn($mockUpdateUser);
//                return $mock;
//            })
//        );

        $response = $this->userService->update($payload, $mockedUser->id);

        $this->assertInstanceOf(User::class, $response);
        $this->assertEquals($mockUpdateUser->firstname, $response->firstname);
        $this->assertEquals($mockUpdateUser->lastname, $response->lastname);
        $this->assertEquals($mockUpdateUser->gender, $response->gender);
    }

    public function testCanDeleteExistingUser()
    {
        $mockedUser = $this->mockUser();

        $this->user->shouldReceive('findOrFail')
            ->once()
            ->with($mockedUser->id)
            ->andReturn($mockedUser);

        $response = $this->userService->delete($mockedUser->id);
        $this->assertNull($response);
    }

    private function mockUser(array|null $data = null): User
    {
        $user = new User();
        $user->id = 1;
        $user->firstname = $data['firstname'] ?? 'John';
        $user->lastname = $data['lastname'] ?? 'Doe';
        $user->gender = $data['gender'] ?? 'male';
        $user->email = 'johndoe@gmail.com';

        return $user;
    }

    public function tearDown():void
    {
        \Mockery::close();
        parent::tearDown();
    }
}
