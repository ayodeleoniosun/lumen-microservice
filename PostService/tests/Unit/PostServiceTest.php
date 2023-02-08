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
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Laravel\Lumen\Testing\DatabaseMigrations;
use Tests\TestCase;
use Tests\Traits\CreatePost;
use Throwable;

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
        $this->post = new Post();
        $this->postLike = new PostLike();
        $this->postService = new PostService($this->post, $this->postLike);
    }

    public function testCanReturnAllPosts()
    {
        $posts = $this->createPost();
        $response = $this->postService->index();

        $this->assertEquals(count($posts), $response->resource->count());
        $this->assertInstanceOf(PostCollection::class, $response);
    }

    public function testCannotUseExistingDetailsToCreateNewPost()
    {
        $this->createNewPost();

        $this->expectException(PostExistException::class);
        $this->createNewPost();
    }

    public function testCanCreateNewPost()
    {
        $payload = $this->newPostPayload();
        $response = $this->postService->create($payload);

        $this->assertInstanceOf(Post::class, $response);
        $this->assertEquals($payload['user_id'], $response->user_id);
        $this->assertEquals($payload['title'], $response->title);
        $this->assertEquals($payload['content'], $response->content);
    }

    public function testCannotShowInvalidPostDetails()
    {
        $this->expectException(ModelNotFoundException::class);
        $this->postService->show(1);
    }

    public function testCanShowPostDetails()
    {
        $post = $this->createNewPost();
        $response = $this->postService->show($post->id);

        $this->assertInstanceOf(PostResource::class, $response);
        $this->assertEquals($post->id, $response->id);
        $this->assertEquals($post->user_id, $response->user_id);
        $this->assertEquals($post->title, $response->title);
        $this->assertEquals($post->content, $response->content);
    }

    public function testCanUpdateExistingPost()
    {
        $post = $this->createNewPost();

        $payload = $this->updatePostPayload();
        $response = $this->postService->update($payload, $post->id);

        $this->assertInstanceOf(Post::class, $response);
        $this->assertEquals($payload['user_id'], $response->user_id);
        $this->assertEquals($payload['title'], $response->title);
        $this->assertEquals($payload['content'], $response->content);
    }

    public function testCannotUpdateUnAuthorizedPost()
    {
        $postOne = $this->createNewPost();
        $payload = $this->updatePostPayload();
        $payload['user_id'] = 2;

        $this->expectException(AuthorizationException::class);
        $this->postService->update($payload, $postOne->id);
    }

    public function testCannotLikeAPostMoreThanOnce()
    {
        $post = $this->createNewPost();
        $this->postService->like(1, $post->id);

        $this->expectException(UserAlreadyLikedPostException::class);
        $this->postService->like(1, $post->id);
    }

    /**
     * @throws Throwable
     * @throws UserAlreadyLikedPostException
     */
    public function testCanLikePost()
    {
        $post = $this->createNewPost();
        $response = $this->postService->like(1, $post->id);

        $this->assertInstanceOf(PostLike::class, $response);
        $this->assertEquals(1, $response->user_id);
        $this->assertEquals(1, $response->post_id);
    }

    public function testCanDeleteExistingPost()
    {
        $post = $this->createNewPost();
        $response = $this->postService->delete($post->id);
        $this->assertNull($response);
    }

    private function newPostPayload(): array
    {
        return [
            'user_id' => 1,
            'title' => 'This is a new post',
            'content' => 'This is the description',
        ];
    }

    private function updatePostPayload(): array
    {
        $payload = $this->newPostPayload();
        $payload['title'] = 'This is an updated post';
        $payload['content'] = 'This is an updated description';

        return $payload;
    }

    private function createNewPost(): Model
    {
        $payload = $this->newPostPayload();

        return $this->postService->create($payload);
    }

    public function tearDown():void
    {
        \Mockery::close();
        parent::tearDown();
    }
}
