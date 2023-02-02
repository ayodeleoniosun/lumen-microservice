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

        $mockedPost = $this->mockPost();

        $this->post->expects('whereUserId')->with($payload['user_id'])->andReturnSelf();
        $this->post->expects('whereTitle')->with($payload['title'])->andReturnSelf();
        $this->post->expects('exists')->andReturnFalse();

        $this->post->expects('create')->andReturn($mockedPost);

        $response = $this->postService->create($payload);

        $this->assertInstanceOf(Post::class, $response);
        $this->assertEquals($mockedPost->id, $response->id);
        $this->assertEquals($mockedPost->user_id, $response->user_id);
        $this->assertEquals($mockedPost->title, $response->title);
        $this->assertEquals($mockedPost->content, $response->content);
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
        $mockedPost = $this->mockPost();

        $this->post->expects('findOrFail')
            ->with($mockedPost->id)
            ->andReturn($mockedPost);

        $response = $this->postService->show($mockedPost->id);

        $this->assertInstanceOf(PostResource::class, $response);
        $this->assertEquals($mockedPost->id, $response->id);
        $this->assertEquals($mockedPost->user_id, $response->user_id);
        $this->assertEquals($mockedPost->title, $response->title);
        $this->assertEquals($mockedPost->content, $response->content);
    }

    public function testCanUpdateExistingPost()
    {
        $mockedPost = $this->mockPost();

        $this->post->shouldReceive('findOrFail')
            ->once()
            ->with($mockedPost->id)
            ->andReturn($mockedPost);

        $payload = [
            'user_id' => 1,
            'title' => 'This is an updated post',
            'content' => 'This is the updated description',
        ];

        $mockUpdatePost = $this->mockPost($payload);

        $response = $this->postService->update($payload, $mockedPost->id);

        $this->assertInstanceOf(Post::class, $response);
        $this->assertEquals($mockUpdatePost->user_id, $response->user_id);
        $this->assertEquals($mockUpdatePost->title, $response->title);
        $this->assertEquals($mockUpdatePost->description, $response->description);
    }

    public function testCannotUpdateUnAuthorizedPost()
    {
        $mockedPost = $this->mockPost();

        $this->post->shouldReceive('findOrFail')
            ->once()
            ->with($mockedPost->id)
            ->andReturn($mockedPost);

        $payload = [
            'user_id' => 2,
            'title' => 'This is an updated post',
            'content' => 'This is the updated description',
        ];

        $this->expectException(AuthorizationException::class);
        $this->expectExceptionMessage('This action is unauthorized.');
        $this->postService->update($payload, $mockedPost->id);
    }

    public function testCannotLikeAPostMoreThanOnce()
    {
        $mockedPost = $this->mockPost();

        $this->postLike->expects('whereUserId')->with($mockedPost->user_id)->andReturnSelf();
        $this->postLike->expects('wherePostId')->with($mockedPost->id)->andReturnSelf();
        $this->postLike->expects('exists')->andReturnTrue();

        $this->expectException(UserAlreadyLikedPostException::class);

        $this->postService->like($mockedPost->user_id, $mockedPost->id);
    }

    /**
     * @throws \Throwable
     * @throws UserAlreadyLikedPostException
     */
    public function testCanLikePost()
    {
        $mockedPost = $this->mockPost();

        $this->postLike->expects('whereUserId')->with($mockedPost->user_id)->andReturnSelf();
        $this->postLike->expects('wherePostId')->with($mockedPost->id)->andReturnSelf();
        $this->postLike->expects('exists')->andReturnFalse();

        $this->post->shouldReceive('findOrFail')
            ->once()
            ->with($mockedPost->id)
            ->andReturn($mockedPost);

        $response = $this->postService->like($mockedPost->user_id, $mockedPost->id);

        $this->assertInstanceOf(PostLike::class, $response);
        $this->assertEquals(1, $response->user_id);
        $this->assertEquals(1, $response->post_id);
    }

    public function testCanDeleteExistingPost()
    {
        $mockedPost = $this->mockPost();

        $this->post->shouldReceive('findOrFail')
            ->once()
            ->with($mockedPost->id)
            ->andReturn($mockedPost);

        $response = $this->postService->delete($mockedPost->id);
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
