<?php

namespace App\Services;

use App\Contracts\PostServiceInterface;
use App\Enums\PostLikeEnum;
use App\Exceptions\UserAlreadyLikePostException;
use App\Http\Resources\PostCollection;
use App\Http\Resources\PostResource;
use App\Models\Post;
use App\Models\PostLike;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class PostService implements PostServiceInterface
{
    /**
     * Get all posts
     *
     * @return Collection
     */
    public function index(): PostCollection
    {
        return new PostCollection(Post::with('likes')->get());
    }

    /**
     * Create new post
     *
     * @return Model
     */
    public function create(array $data): Model
    {
        return Post::create($data);
    }

    /**
     *  Show post details
     * @return Model
     */
    public function show(int $post): PostResource
    {
        return new PostResource(Post::findOrFail($post));
    }

    /**
     *  Update post details
     * @return Model
     */
    public function update(array $data, int $id): Model
    {
        $post = Post::findOrFail($id);

        $post->title = $data['title'];
        $post->content = $data['content'];
        $post->save();

        return $post;
    }

    /**
     * @param int $user
     * @param int $id
     * @return Model
     * @throws UserAlreadyLikePostException
     */
    public function like(int $user, int $id): Model
    {
        $hasLiked = PostLike::where([
            'user_id' => $user,
            'post_id' => $id,
        ])->exists();

        if ($hasLiked) {
            throw new UserAlreadyLikePostException();
        }

        $post = Post::findOrFail($id);

        return $post->likes()->create([
            'user_id' => $user,
            'status' => PostLikeEnum::LIKE,
        ]);
    }

    /**
     *  Remove post details
     * @param int $id
     * @return void
     */
    public function delete(int $id): void
    {
        $post = Post::findOrFail($id);
        $post->delete();
    }
}
