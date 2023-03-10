<?php

namespace Tests\Feature;

use Illuminate\Http\Response;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Tests\TestCase;
use Tests\Traits\CreateComment;

class CommentControllerTest extends TestCase
{
    use CreateComment;
    use DatabaseMigrations;

    public string $userId;

    public string $commentId;

    protected function setup(): void
    {
        parent::setUp();
        $this->userId = 1;
        $this->postId = 1;
    }

    public function testShouldReturnAllPostComments()
    {
        $comment = $this->createComment();
        $response = $this->get($this->baseUrl . "/comments/posts/{$comment[0]->post_id}", $this->withHeaders());
        $data = $this->responseData($response);

        $this->assertCount(10, $data);

        $response->assertResponseOk();
        $response->seeJsonStructure([
           '*' => [
                'id', 'user_id', 'comment', 'count_likes', 'created_at', 'updated_at'
            ]
        ]);
    }

    public function testIncompletePayloadShouldNotCreateNewComment()
    {
        $response = $this->post($this->baseUrl . '/comments', [
            'user_id' => $this->userId,
            'post_id' => $this->postId,
        ], $this->withHeaders());

        $data = $this->responseData($response);

        $this->assertEquals('error', $data->status);
        $this->assertEquals('The comment field is required.', $data->message);
        $this->assertResponseStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }


    public function testShouldCreateNewComment()
    {
        $payload = $this->newCommentPayload();
        $data = $this->createNewCommentAndReturnData($payload);

        $this->assertEquals($payload['user_id'], $data->user_id);
        $this->assertEquals($payload['post_id'], $data->post_id);
        $this->assertEquals($payload['comment'], $data->comment);
    }

    public function testCommentNotFound()
    {
        $response = $this->get($this->baseUrl . "/comments/1", $this->withHeaders());
        $data = $this->responseData($response);

        $this->assertEquals('error', $data->status);
        $this->assertEquals('Comment not found', $data->message);
        $this->assertResponseStatus(Response::HTTP_NOT_FOUND);
    }

    public function testShouldShowCommentDetails()
    {
        $payload = $this->newCommentPayload();
        $data = $this->createNewCommentAndReturnData($payload);

        $response = $this->get($this->baseUrl . "/comments/{$data->id}", $this->withHeaders());
        $commentResponse = $this->responseData($response);

        $this->assertEquals($data->id, $commentResponse->id);
        $this->assertEquals($data->user_id, $commentResponse->user_id);
        $this->assertEquals($data->comment, $commentResponse->comment);
        $this->assertResponseStatus(Response::HTTP_OK);
    }

    public function testShouldNotUpdateInvalidComment()
    {
        $payload = $this->newCommentPayload();
        $data = $this->updateCommentAndReturnData(1, $payload);

        $this->assertEquals('error', $data->status);
        $this->assertEquals('Comment not found', $data->message);
        $this->assertResponseStatus(Response::HTTP_NOT_FOUND);
    }

    public function testShouldUpdateComment()
    {
        $payload = $this->newCommentPayload();
        $data = $this->createNewCommentAndReturnData($payload);
        $payload['comment'] = 'this is the updated comment';

        $commentResponse = $this->updateCommentAndReturnData($data->id, $payload);

        $this->assertEquals($data->id, $commentResponse->id);
        $this->assertEquals('this is the updated comment', $commentResponse->comment);
        $this->assertResponseStatus(Response::HTTP_OK);
    }

    public function testShouldNotLikeAnInvalidComment()
    {
        $commentResponse = $this->likeCommentAndReturnData(1);

        $this->assertEquals('error', $commentResponse->status);
        $this->assertEquals('Comment not found', $commentResponse->message);
        $this->assertResponseStatus(Response::HTTP_NOT_FOUND);
    }

    public function testShouldNotLikeAnAlreadyLikedComment()
    {
        $payload = $this->newCommentPayload();
        $comment = $this->createNewCommentAndReturnData($payload);
        $this->likeCommentAndReturnData($comment->id);
        $commentResponse = $this->likeCommentAndReturnData($comment->id);

        $this->assertEquals('error', $commentResponse->status);
        $this->assertEquals('You have already liked this comment.', $commentResponse->message);
        $this->assertResponseStatus(Response::HTTP_BAD_REQUEST);
    }

    public function testShouldLikeAComment()
    {
        $payload = $this->newCommentPayload();
        $comment = $this->createNewCommentAndReturnData($payload);
        $commentResponse = $this->likeCommentAndReturnData($comment->id);

        $this->assertEquals($comment->id, $commentResponse->comment_id);
        $this->assertEquals($this->userId, $commentResponse->user_id);
    }

    public function testShouldNotDeleteInvalidComment()
    {
        $response = $this->get($this->baseUrl . "/comments/delete/1", $this->withHeaders());
        $commentResponse = $this->responseData($response);

        $this->assertEquals('error', $commentResponse->status);
        $this->assertEquals('Comment not found', $commentResponse->message);
        $this->assertResponseStatus(Response::HTTP_NOT_FOUND);
    }

    public function testShouldDeleteComment()
    {
        $payload = $this->newCommentPayload();
        $data = $this->createNewCommentAndReturnData($payload);
        $this->get($this->baseUrl . "/comments/delete/{$data->id}", $this->withHeaders());

        $this->assertResponseStatus(Response::HTTP_NO_CONTENT);
    }

    private function createNewCommentAndReturnData(array $payload)
    {
        $response = $this->post($this->baseUrl . '/comments', $payload, $this->withHeaders());
        return $this->responseData($response);
    }

    private function updateCommentAndReturnData(int $id, array $payload)
    {
        $response = $this->put($this->baseUrl . "/comments/{$id}", $payload, $this->withHeaders());
        return $this->responseData($response);
    }

    private function likeCommentAndReturnData(int $id)
    {
        $response = $this->post($this->baseUrl . "/comments/{$id}/like", [
            'user_id' => 1,
        ], $this->withHeaders());

        return $this->responseData($response);
    }

    private function newCommentPayload(): array
    {
        return [
            'user_id' => $this->userId,
            'post_id' => $this->postId,
            'comment' => 'this is a new comment',
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
