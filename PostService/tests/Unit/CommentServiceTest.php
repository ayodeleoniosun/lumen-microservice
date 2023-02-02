<?php

namespace Tests\Unit;

use App\Exceptions\UserAlreadyLikedCommentException;
use App\Http\Resources\Comment\CommentCollection;
use App\Http\Resources\Comment\CommentResource;
use App\Models\Comment\Comment;
use App\Models\Comment\CommentLike;
use App\Services\CommentService;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Tests\TestCase;
use Tests\Traits\CreateComment;

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
        $this->comment = \Mockery::mock(Comment::class)->makePartial();
        $this->commentLike = \Mockery::mock(CommentLike::class)->makePartial();
        $this->commentService = new CommentService($this->comment, $this->commentLike);
    }

    public function testCanReturnAllPostComments()
    {
        $comments = new CommentCollection([$this->comment]);
        $comment = $this->mockComment();

        $this->comment->expects('wherePostId')->with($comment->post_id)->andReturnSelf();
        $this->comment->expects('with')->with('likes')->andReturnSelf();
        $this->comment->expects('get')->andReturn($comments);

        $response = $this->commentService->index($comment->post_id);
        $this->assertInstanceOf(CommentCollection::class, $response);
    }

    public function testCanCreateNewComment()
    {
        $payload = [
            'user_id' => 1,
        ];

        $comment = $this->mockComment();
        $this->comment->expects('create')->andReturn($comment);
        $response = $this->commentService->create($payload);

        $this->assertInstanceOf(Comment::class, $response);
        $this->assertEquals($comment->id, $response->id);
        $this->assertEquals($comment->user_id, $response->user_id);
        $this->assertEquals($comment->post_id, $response->post_id);
        $this->assertEquals($comment->comment, $response->comment);
    }

    public function testCannotShowInvalidCommentDetails()
    {
        $this->comment->expects('findOrFail')
            ->with(1)
            ->andThrows(ModelNotFoundException::class, 'Comment not found');

        $this->expectException(ModelNotFoundException::class);
        $this->expectExceptionMessage('Comment not found');

        $this->commentService->show(1);
    }

    public function testCanShowCommentDetails()
    {
        $comment = $this->mockComment();

        $this->comment->expects('findOrFail')
            ->with($comment->id)
            ->andReturn($comment);

        $response = $this->commentService->show($comment->id);

        $this->assertInstanceOf(CommentResource::class, $response);
        $this->assertEquals($comment->id, $response->id);
        $this->assertEquals($comment->user_id, $response->user_id);
        $this->assertEquals($comment->post_id, $response->post_id);
        $this->assertEquals($comment->comment, $response->comment);
    }

    public function testCanUpdateExistingComment()
    {
        $comment = $this->mockComment();

        $this->comment->shouldReceive('findOrFail')
            ->once()
            ->with($comment->id)
            ->andReturn($comment);

        $payload = [
            'user_id' => 1,
            'post_id' => 1,
            'comment' => 'This is the updated comment',
        ];

        $updatedComment = $this->mockComment($payload);

        $response = $this->commentService->update($payload, $comment->id);

        $this->assertInstanceOf(Comment::class, $response);
        $this->assertEquals($updatedComment->user_id, $response->user_id);
        $this->assertEquals($updatedComment->post_id, $response->post_id);
        $this->assertEquals($updatedComment->comment, $response->comment);
    }

    public function testCannotUpdateUnAuthorizedComment()
    {
        $comment = $this->mockComment();

        $this->comment->shouldReceive('findOrFail')
            ->once()
            ->with($comment->id)
            ->andReturn($comment);

        $payload = [
            'user_id' => 2,
            'post_id' => 1,
            'comment' => 'This is the updated comment',
        ];

        $this->expectException(AuthorizationException::class);
        $this->expectExceptionMessage('This action is unauthorized.');
        $this->commentService->update($payload, $comment->id);
    }

    public function testCannotLikeACommentMoreThanOnce()
    {
        $comment = $this->mockComment();

        $this->commentLike->expects('whereUserId')->with($comment->user_id)->andReturnSelf();
        $this->commentLike->expects('whereCommentId')->with($comment->id)->andReturnSelf();
        $this->commentLike->expects('exists')->andReturnTrue();

        $this->expectException(UserAlreadyLikedCommentException::class);

        $this->commentService->like($comment->user_id, $comment->id);
    }

    /**
     * @throws \Throwable
     * @throws UserAlreadyLikedCommentException
     */
    public function testCanLikeComment()
    {
        $comment = $this->mockComment();

        $this->commentLike->expects('whereUserId')->with($comment->user_id)->andReturnSelf();
        $this->commentLike->expects('whereCommentId')->with($comment->id)->andReturnSelf();
        $this->commentLike->expects('exists')->andReturnFalse();

        $this->comment->shouldReceive('findOrFail')
            ->once()
            ->with($comment->id)
            ->andReturn($comment);

        $response = $this->commentService->like($comment->user_id, $comment->id);

        $this->assertInstanceOf(CommentLike::class, $response);
        $this->assertEquals(1, $response->user_id);
        $this->assertEquals(1, $response->comment_id);
    }

    public function testCanDeleteExistingComment()
    {
        $comment = $this->mockComment();

        $this->comment->shouldReceive('findOrFail')
            ->once()
            ->with($comment->id)
            ->andReturn($comment);

        $response = $this->commentService->delete($comment->id);
        $this->assertNull($response);
    }

    private function mockComment(array|null $data = null): Comment
    {
        $comment = new Comment();
        $comment->id = 1;
        $comment->user_id = $data['user_id'] ?? 1;
        $comment->post_id = $data['post_id'] ?? 1;
        $comment->comment = $data['comment'] ?? 'This is the comment';

        return $comment;
    }

    public function tearDown():void
    {
        \Mockery::close();
        parent::tearDown();
    }
}
