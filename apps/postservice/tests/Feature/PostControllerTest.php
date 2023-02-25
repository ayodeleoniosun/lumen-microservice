<?php

namespace Tests\Feature;

use Illuminate\Http\Response;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Tests\TestCase;
use Tests\Traits\CreatePost;

class PostControllerTest extends TestCase
{
    use CreatePost;
    use DatabaseMigrations;
    public string $userId;

    protected function setup(): void
    {
        parent::setUp();

        $this->userId = 1;
    }

    public function testShouldReturnAllPosts()
    {
        $this->createPost();
        $response = $this->get($this->baseUrl . '/posts', $this->withHeaders());
        $data = $this->responseData($response);

        $this->assertCount(10, $data);

        $response->assertResponseOk();
        $response->seeJsonStructure([
            '*' => [
                'id', 'user_id', 'title', 'content', 'count_likes', 'created_at', 'updated_at'
            ]
        ]);
    }

    public function testIncompletePayloadShouldNotCreateNewPost()
    {
        $response = $this->post($this->baseUrl . '/posts', [
            'user_id' => $this->userId,
            'title' => 'this is a new post',
        ], $this->withHeaders());

        $data = $this->responseData($response);

        $this->assertEquals('error', $data->status);
        $this->assertEquals('The content field is required.', $data->message);
        $this->assertResponseStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function testExistingRecordShouldNotCreateNewPost()
    {
        $payload = $this->newPostPayload();
        $this->post($this->baseUrl . '/posts', $payload, $this->withHeaders());
        $data = $this->createNewPostAndReturnData($payload);

        $this->assertEquals('error', $data->status);
        $this->assertEquals('You have already created this post.', $data->message);
        $this->assertResponseStatus(Response::HTTP_BAD_REQUEST);
    }

    public function testShouldCreateNewPost()
    {
        $payload = $this->newPostPayload();
        $data = $this->createNewPostAndReturnData($payload);

        $this->assertEquals($payload['user_id'], $data->user_id);
        $this->assertEquals($payload['title'], $data->title);
        $this->assertEquals($payload['content'], $data->content);
    }

    public function testPostNotFound()
    {
        $response = $this->get($this->baseUrl . "/posts/1", $this->withHeaders());
        $data = $this->responseData($response);

        $this->assertEquals('error', $data->status);
        $this->assertEquals('Post not found', $data->message);
        $this->assertResponseStatus(Response::HTTP_NOT_FOUND);
    }

    public function testShouldShowPostDetails()
    {
        $payload = $this->newPostPayload();
        $data = $this->createNewPostAndReturnData($payload);

        $response = $this->get($this->baseUrl . "/posts/{$data->id}", $this->withHeaders());
        $postResponse = $this->responseData($response);

        $this->assertEquals($data->id, $postResponse->id);
        $this->assertEquals($data->title, $postResponse->title);
        $this->assertEquals($data->content, $postResponse->content);
    }

    public function testShouldNotUpdateInvalidPost()
    {
        $payload = $this->newPostPayload();
        $data = $this->updatePostAndReturnData(1, $payload);

        $this->assertEquals('error', $data->status);
        $this->assertEquals('Post not found', $data->message);
        $this->assertResponseStatus(Response::HTTP_NOT_FOUND);
    }

    public function testShouldUpdatePost()
    {
        $payload = $this->newPostPayload();
        $data = $this->createNewPostAndReturnData($payload);

        $payload['title'] = 'this is the updated post';
        $payload['content'] = 'this is the updated description';
        $postResponse = $this->updatePostAndReturnData($data->id, $payload);

        $this->assertEquals($data->id, $postResponse->id);
        $this->assertEquals('this is the updated post', $postResponse->title);
        $this->assertEquals('this is the updated description', $postResponse->content);
        $this->assertResponseStatus(Response::HTTP_OK);
    }

    public function testShouldNotLikeAnInvalidPost()
    {
        $postResponse = $this->likePostAndReturnData(1);

        $this->assertEquals('error', $postResponse->status);
        $this->assertEquals('Post not found', $postResponse->message);
        $this->assertResponseStatus(Response::HTTP_NOT_FOUND);
    }

    public function testShouldNotLikeAnAlreadyLikedPost()
    {
        $payload = $this->newPostPayload();
        $post = $this->createNewPostAndReturnData($payload);

        $this->likePostAndReturnData($post->id);
        $postResponse = $this->likePostAndReturnData($post->id);

        $this->assertEquals('error', $postResponse->status);
        $this->assertEquals('You have already liked this post.', $postResponse->message);
        $this->assertResponseStatus(Response::HTTP_BAD_REQUEST);
    }

    public function testShouldLikeAPost()
    {
        $payload = $this->newPostPayload();
        $post = $this->createNewPostAndReturnData($payload);
        $postResponse = $this->likePostAndReturnData($post->id);

        $this->assertEquals($post->id, $postResponse->post_id);
        $this->assertEquals(1, $postResponse->user_id);
    }

    public function testShouldNotDeleteInvalidPost()
    {
        $response = $this->get($this->baseUrl . "/posts/delete/1", $this->withHeaders());
        $postResponse = $this->responseData($response);

        $this->assertEquals('error', $postResponse->status);
        $this->assertEquals('Post not found', $postResponse->message);
        $this->assertResponseStatus(Response::HTTP_NOT_FOUND);
    }

    public function testShouldDeletePost()
    {
        $payload = $this->newPostPayload();
        $data = $this->createNewPostAndReturnData($payload);
        $this->get($this->baseUrl . "/posts/delete/{$data->id}", $this->withHeaders());

        $this->assertResponseStatus(Response::HTTP_NO_CONTENT);
    }

    private function createNewPostAndReturnData(array $payload)
    {
        $response = $this->post($this->baseUrl . '/posts', $payload, $this->withHeaders());
        return $this->responseData($response);
    }

    private function updatePostAndReturnData(int $id, array $payload)
    {
        $response = $this->put($this->baseUrl . "/posts/{$id}", $payload, $this->withHeaders());
        return $this->responseData($response);
    }

    private function likePostAndReturnData(int $id)
    {
        $response = $this->post($this->baseUrl . "/posts/{$id}/like", [
            'user_id' => 1,
        ], $this->withHeaders());

        return $this->responseData($response);
    }

    private function newPostPayload(): array
    {
        return [
            'user_id' => $this->userId,
            'title' => 'this is a new post',
            'content' => 'this is a new post description',
        ];
    }

    private function withHeaders(): array {
        return [
            'x-api-key' => config('services.api_token.secret'),
            'Accept' => 'application/json'
        ];
    }

    public function tearDown():void
    {
        parent::tearDown();
    }
}
