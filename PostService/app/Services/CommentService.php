<?php

namespace App\Services;

use App\Contracts\CommentServiceInterface;
use App\Exceptions\UserAlreadyLikedCommentException;
use App\Http\Resources\Comment\CommentCollection;
use App\Http\Resources\Comment\CommentResource;
use App\Models\Comment\Comment;
use App\Models\Comment\CommentLike;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\Model;
use Throwable;

class CommentService implements CommentServiceInterface
{
    public Comment $comment;
    public CommentLike $commentLike;

    /**
     * @param Comment $comment
     * @param CommentLike $commentLike
     */
    public function __construct(Comment $comment, CommentLike $commentLike)
    {
        $this->comment = $comment;
        $this->commentLike = $commentLike;
    }

    /**
     * Get all comments
     *
     * @return CommentCollection
     */
    public function index(int $post): CommentCollection
    {
        return new CommentCollection($this->comment->wherePostId($post)->with('likes')->get());
    }

    /**
     * Create new comment
     *
     * @return Model
     */
    public function create(array $data): Model
    {
        return $this->comment->create($data);
    }

    /**
     *  Show comment details
     * @param int $comment
     * @return CommentResource
     */
    public function show(int $comment): CommentResource
    {
        return new CommentResource($this->comment->findOrFail($comment));
    }

    /**
     *  Update comment details
     * @param array $data
     * @param int $id
     * @return Model
     * @throws AuthorizationException
     * @throws Throwable
     */
    public function update(array $data, int $id): Model
    {
        $comment = $this->comment->findOrFail($id);

        throw_if((int) $data['user_id'] !== $comment->user_id, AuthorizationException::class);

        $comment->comment = $data['comment'];
        $comment->save();

        return $comment;
    }

    /**
     * @param int $user
     * @param int $id
     * @return Model
     * @throws UserAlreadyLikedCommentException
     * @throws Throwable
     */
    public function like(int $user, int $id): Model
    {
        $hasLiked = $this->commentLike->whereUserId($user)->whereCommentId($id)->exists();

        throw_if($hasLiked, UserAlreadyLikedCommentException::class);

        $comment = $this->comment->findOrFail($id);

        return $comment->likes()->create([
            'user_id' => $user,
        ]);
    }

    /**
     *  Remove comment details
     * @return void
     */
    public function delete(int $id): void
    {
        $comment = $this->comment->findOrFail($id);
        $comment->delete();
    }
}
