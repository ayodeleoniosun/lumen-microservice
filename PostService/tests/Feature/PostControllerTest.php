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
        $response = $this->get($this->baseUrl . '/posts');
        $data = $this->responseData($response);

        $this->assertCount(10, $data->data);
        $this->assertEquals('success', $data->status);

        $response->assertResponseOk();
        $response->seeJsonStructure([
            'status', 'message',
            'data' => [
                '*' => [
                    'id', 'user_id', 'title', 'content', 'count_likes', 'created_at', 'updated_at'
                ]
            ],
        ]);
    }

    public function testIncompletePayloadShouldNotCreateNewPost()
    {
        $response = $this->post($this->baseUrl . '/posts', [
            'user_id' => $this->userId,
            'title' => 'this is a new post',
        ]);

        $data = $this->responseData($response);

        $this->assertEquals('error', $data->status);
        $this->assertEquals('The content field is required.', $data->message);
        $this->assertResponseStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    public function testExistingRecordShouldNotCreateNewPost()
    {
        $payload = $this->newPostPayload();
        $this->post($this->baseUrl . '/posts', $payload);
        $data = $this->createNewPostAndReturnData($payload);

        $this->assertEquals('error', $data->status);
        $this->assertEquals('You have already created this post.', $data->message);
        $this->assertResponseStatus(Response::HTTP_BAD_REQUEST);
    }

    public function testShouldCreateNewPost()
    {
        $payload = $this->newPostPayload();
        $data = $this->createNewPostAndReturnData($payload);

        $this->assertEquals('success', $data->status);
        $this->assertEquals('Post successfully created', $data->message);
        $this->assertEquals($payload['user_id'], $data->data->user_id);
        $this->assertEquals($payload['title'], $data->data->title);
        $this->assertEquals($payload['content'], $data->data->content);
        $this->assertResponseStatus(Response::HTTP_CREATED);
    }

    public function testPostNotFound()
    {
        $response = $this->get($this->baseUrl . "/posts/1");
        $data = $this->responseData($response);

        $this->assertEquals('error', $data->status);
        $this->assertEquals('Post not found', $data->message);
        $this->assertResponseStatus(Response::HTTP_NOT_FOUND);
    }

    public function testShouldShowPostDetails()
    {
        $payload = $this->newPostPayload();
        $data = $this->createNewPostAndReturnData($payload);

        $response = $this->get($this->baseUrl . "/posts/{$data->data->id}");
        $postResponse = $this->responseData($response);

        $this->assertEquals('success', $postResponse->status);
        $this->assertEquals($data->data->id, $postResponse->data->id);
        $this->assertEquals($data->data->title, $postResponse->data->title);
        $this->assertEquals($data->data->content, $postResponse->data->content);
        $this->assertResponseStatus(Response::HTTP_OK);
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
        $postResponse = $this->updatePostAndReturnData($data->data->id, $payload);

        $this->assertEquals('success', $postResponse->status);
        $this->assertEquals('Post successfully updated', $postResponse->message);
        $this->assertEquals($data->data->id, $postResponse->data->id);
        $this->assertEquals('this is the updated post', $postResponse->data->title);
        $this->assertEquals('this is the updated description', $postResponse->data->content);
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

        $this->likePostAndReturnData($post->data->id);
        $postResponse = $this->likePostAndReturnData($post->data->id);

        $this->assertEquals('error', $postResponse->status);
        $this->assertEquals('You have already liked this post.', $postResponse->message);
        $this->assertResponseStatus(Response::HTTP_BAD_REQUEST);
    }

    public function testShouldLikeAPost()
    {
        $payload = $this->newPostPayload();
        $post = $this->createNewPostAndReturnData($payload);
        $postResponse = $this->likePostAndReturnData($post->data->id);

        $this->assertEquals('success', $postResponse->status);
        $this->assertEquals('Post liked', $postResponse->message);
        $this->assertEquals($post->data->id, $postResponse->data->post_id);
        $this->assertEquals(1, $postResponse->data->user_id);
        $this->assertResponseStatus(Response::HTTP_CREATED);
    }

    public function testShouldNotDeleteInvalidPost()
    {
        $response = $this->delete($this->baseUrl . "/posts/1");
        $postResponse = $this->responseData($response);

        $this->assertEquals('error', $postResponse->status);
        $this->assertEquals('Post not found', $postResponse->message);
        $this->assertResponseStatus(Response::HTTP_NOT_FOUND);
    }

    public function testShouldDeletePost()
    {
        $payload = $this->newPostPayload();
        $data = $this->createNewPostAndReturnData($payload);
        $this->delete($this->baseUrl . "/posts/{$data->data->id}");

        $this->assertResponseStatus(Response::HTTP_NO_CONTENT);
    }

    private function createNewPostAndReturnData(array $payload)
    {
        $response = $this->post($this->baseUrl . '/posts', $payload);
        return $this->responseData($response);
    }

    private function updatePostAndReturnData(int $id, array $payload)
    {
        $response = $this->put($this->baseUrl . "/posts/{$id}", $payload);
        return $this->responseData($response);
    }

    private function likePostAndReturnData(int $id)
    {
        $response = $this->post($this->baseUrl . "/posts/{$id}/like", [
            'user_id' => 1,
        ]);

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

    public function tearDown():void
    {
        parent::tearDown();
    }
}
