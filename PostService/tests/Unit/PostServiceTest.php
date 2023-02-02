<?php

namespace Tests\Unit;

use App\Exceptions\PostExistException;
use App\Exceptions\UserAlreadyLikedPostException;
use App\Http\Resources\Post\PostCollection;
use App\Http\Resources\Post\PostResource;
use App\Models\Post\Post;
use App\Models\Post\PostLike;
use App\Services\PostService;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Tests\TestCase;
use Tests\Traits\CreatePost;

class PostServiceTest extends TestCase
{
    use CreatePost;
    use DatabaseMigrations;

    public PostService $postService;
    public Post $post;
    public PostLike $postLike;

    protected function setup(): void
    {
        parent::setUp();
        $this->post = \Mockery::mock(Post::class)->makePartial();
        $this->postLike = \Mockery::mock(PostLike::class)->makePartial();
        $this->postService = new PostService($this->post, $this->postLike);
    }

    public function testCanReturnAllPosts()
    {
        $posts = new PostCollection([$this->post]);

        $this->post->expects('with')->with('likes')->andReturnSelf();
        $this->post->expects('get')->andReturn($posts);

        $response = $this->postService->index();
        $this->assertInstanceOf(PostCollection::class, $response);
    }

    public function testCannotUseExistingDetailsToCreateNewPost()
    {
        $payload = [
            'user_id' => 1,
            'title' => 'This is a new post',
            'content' => 'This is the description',
        ];

        $this->post->expects('whereUserId')->with($payload['user_id'])->andReturnSelf();
        $this->post->expects('whereTitle')->with($payload['title'])->andReturnSelf();
        $this->post->expects('exists')
            ->andReturnTrue()
            ->andThrows(PostExistException::class, 'You have already created this post.');

        $this->expectException(PostExistException::class);
        $this->expectExceptionMessage('You have already created this post.');
        $this->postService->create($payload);
    }

    public function testCanCreateNewPost()
    {
        $payload = [
            'user_id' => 1,
            'title' => 'This is a new post',
            'content' => 'This is the description',
        ];

        $post = $this->mockPost();

        $this->post->expects('whereUserId')->with($payload['user_id'])->andReturnSelf();
        $this->post->expects('whereTitle')->with($payload['title'])->andReturnSelf();
        $this->post->expects('exists')->andReturnFalse();

        $this->post->expects('create')->andReturn($post);

        $response = $this->postService->create($payload);

        $this->assertInstanceOf(Post::class, $response);
        $this->assertEquals($post->id, $response->id);
        $this->assertEquals($post->user_id, $response->user_id);
        $this->assertEquals($post->title, $response->title);
        $this->assertEquals($post->content, $response->content);
    }

    public function testCannotShowInvalidPostDetails()
    {
        $this->post->expects('findOrFail')
            ->with(1)
            ->andThrows(ModelNotFoundException::class, 'Post not found');

        $this->expectException(ModelNotFoundException::class);
        $this->expectExceptionMessage('Post not found');

        $this->postService->show(1);
    }

    public function testCanShowPostDetails()
    {
        $post = $this->mockPost();

        $this->post->expects('findOrFail')
            ->with($post->id)
            ->andReturn($post);

        $response = $this->postService->show($post->id);

        $this->assertInstanceOf(PostResource::class, $response);
        $this->assertEquals($post->id, $response->id);
        $this->assertEquals($post->user_id, $response->user_id);
        $this->assertEquals($post->title, $response->title);
        $this->assertEquals($post->content, $response->content);
    }

    public function testCanUpdateExistingPost()
    {
        $post = $this->mockPost();

        $this->post->shouldReceive('findOrFail')
            ->once()
            ->with($post->id)
            ->andReturn($post);

        $payload = [
            'user_id' => 1,
            'title' => 'This is an updated post',
            'content' => 'This is the updated description',
        ];

        $updatedPost = $this->mockPost($payload);

        $response = $this->postService->update($payload, $post->id);

        $this->assertInstanceOf(Post::class, $response);
        $this->assertEquals($updatedPost->user_id, $response->user_id);
        $this->assertEquals($updatedPost->title, $response->title);
        $this->assertEquals($updatedPost->description, $response->description);
    }

    public function testCannotUpdateUnAuthorizedPost()
    {
        $post = $this->mockPost();

        $this->post->shouldReceive('findOrFail')
            ->once()
            ->with($post->id)
            ->andReturn($post);

        $payload = [
            'user_id' => 2,
            'title' => 'This is an updated post',
            'content' => 'This is the updated description',
        ];

        $this->expectException(AuthorizationException::class);
        $this->expectExceptionMessage('This action is unauthorized.');
        $this->postService->update($payload, $post->id);
    }

    public function testCannotLikeAPostMoreThanOnce()
    {
        $post = $this->mockPost();

        $this->postLike->expects('whereUserId')->with($post->user_id)->andReturnSelf();
        $this->postLike->expects('wherePostId')->with($post->id)->andReturnSelf();
        $this->postLike->expects('exists')->andReturnTrue();

        $this->expectException(UserAlreadyLikedPostException::class);

        $this->postService->like($post->user_id, $post->id);
    }

    /**
     * @throws \Throwable
     * @throws UserAlreadyLikedPostException
     */
    public function testCanLikePost()
    {
        $post = $this->mockPost();

        $this->postLike->expects('whereUserId')->with($post->user_id)->andReturnSelf();
        $this->postLike->expects('wherePostId')->with($post->id)->andReturnSelf();
        $this->postLike->expects('exists')->andReturnFalse();

        $this->post->shouldReceive('findOrFail')
            ->once()
            ->with($post->id)
            ->andReturn($post);

        $response = $this->postService->like($post->user_id, $post->id);

        $this->assertInstanceOf(PostLike::class, $response);
        $this->assertEquals(1, $response->user_id);
        $this->assertEquals(1, $response->post_id);
    }

    public function testCanDeleteExistingPost()
    {
        $post = $this->mockPost();

        $this->post->shouldReceive('findOrFail')
            ->once()
            ->with($post->id)
            ->andReturn($post);

        $response = $this->postService->delete($post->id);
        $this->assertNull($response);
    }

    private function mockPost(array|null $data = null): Post
    {
        $post = new Post();
        $post->id = 1;
        $post->user_id = $data['user_id'] ?? 1;
        $post->title = $data['title'] ?? 'This is a new post';
        $post->content = $data['content'] ?? 'This is the description';

        return $post;
    }

    public function tearDown():void
    {
        \Mockery::close();
        parent::tearDown();
    }
}
