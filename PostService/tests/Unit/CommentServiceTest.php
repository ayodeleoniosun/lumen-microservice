<?php

namespace Tests\Unit;

use App\Exceptions\UserAlreadyLikedCommentException;
use App\Http\Resources\Comment\CommentCollection;
use App\Http\Resources\Comment\CommentResource;
use App\Models\Comment\Comment;
use App\Models\Comment\CommentLike;
use App\Services\CommentService;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Tests\TestCase;
use Tests\Traits\CreateComment;
use Throwable;

class CommentServiceTest extends TestCase
{
    use CreateComment;
    use DatabaseMigrations;

    public CommentService $commentService;
    public Comment $comment;
    public CommentLike $commentLike;

    protected function setup(): void
    {
        parent::setUp();
        $this->comment = new Comment();
        $this->commentLike = new CommentLike();
        $this->commentService = new CommentService($this->comment, $this->commentLike);
    }

    public function testCanReturnAllPostComments()
    {
        $comments = $this->createComment();
        $response = $this->commentService->index(1);

        $this->assertEquals(count($comments), $response->resource->count());
        $this->assertInstanceOf(CommentCollection::class, $response);
    }

    public function testCanCreateNewComment()
    {
        $payload = $this->newCommentPayload();
        $response = $this->commentService->create($payload);

        $this->assertInstanceOf(Comment::class, $response);
        $this->assertEquals($payload['user_id'], $response->user_id);
        $this->assertEquals($payload['post_id'], $response->post_id);
        $this->assertEquals($payload['comment'], $response->comment);
    }

    public function testCannotShowInvalidCommentDetails()
    {
        $this->expectException(ModelNotFoundException::class);
        $this->commentService->show(1);
    }

    public function testCanShowCommentDetails()
    {
        $comment = $this->createNewComment();
        $response = $this->commentService->show($comment->id);

        $this->assertInstanceOf(CommentResource::class, $response);
        $this->assertEquals($comment->id, $response->id);
        $this->assertEquals($comment->user_id, $response->user_id);
        $this->assertEquals($comment->post_id, $response->post_id);
        $this->assertEquals($comment->comment, $response->comment);
    }

    public function testCanUpdateExistingComment()
    {
        $comment = $this->createNewComment();

        $payload = $this->updateCommentPayload();
        $response = $this->commentService->update($payload, $comment->id);

        $this->assertInstanceOf(Comment::class, $response);
        $this->assertEquals($payload['user_id'], $response->user_id);
        $this->assertEquals($payload['post_id'], $response->post_id);
        $this->assertEquals($payload['comment'], $response->comment);
    }

    public function testCannotUpdateUnAuthorizedComment()
    {
        $commentOne = $this->createNewComment();
        $payload = $this->updateCommentPayload();
        $payload['user_id'] = 2;

        $this->expectException(AuthorizationException::class);
        $this->commentService->update($payload, $commentOne->id);
    }

    public function testCannotLikeACommentMoreThanOnce()
    {
        $comment = $this->createNewComment();
        $this->commentService->like(1, $comment->id);

        $this->expectException(UserAlreadyLikedCommentException::class);
        $this->commentService->like(1, $comment->id);
    }

    /**
     * @throws Throwable
     * @throws UserAlreadyLikedCommentException
     */
    public function testCanLikeComment()
    {
        $comment = $this->createNewComment();
        $response = $this->commentService->like(1, $comment->id);

        $this->assertInstanceOf(CommentLike::class, $response);
        $this->assertEquals(1, $response->user_id);
        $this->assertEquals(1, $response->comment_id);
    }

    public function testCanDeleteExistingComment()
    {
        $comment = $this->createNewComment();
        $response = $this->commentService->delete($comment->id);
        $this->assertNull($response);
    }

    private function newCommentPayload(): array
    {
        return [
            'user_id' => 1,
            'post_id' => 1,
            'comment' => 'This is the comment',
        ];
    }

    private function updateCommentPayload(): array
    {
        $payload = $this->newCommentPayload();
        $payload['comment'] = 'This is the updated comment';
        return $payload;
    }

    private function createNewComment(): Model
    {
        $payload = $this->newCommentPayload();

        return $this->commentService->create($payload);
    }

    public function tearDown():void
    {
        \Mockery::close();
        parent::tearDown();
    }
}
