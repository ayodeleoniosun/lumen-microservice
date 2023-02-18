<?php

namespace App\Services;

use App\Contracts\CommentServiceInterface;
use App\Exceptions\UserAlreadyLikedCommentException;
use App\Http\Resources\CommentCollection;
use App\Http\Resources\CommentResource;
use App\Models\Comment;
use App\Models\CommentLike;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\Model;
use Throwable;

class CommentService implements CommentServiceInterface
{

    /**
     * Get all comments
     *
     * @return CommentCollection
     */
    public function index(int $post): CommentCollection
    {
        return new CommentCollection(Comment::wherePostId($post)->with('likes')->get());
    }

    /**
     * Create new comment
     *
     * @return Model
     */
    public function create(array $data): Model
    {
        return Comment::create($data);
    }

    /**
     *  Show comment details
     * @param int $comment
     * @return CommentResource
     */
    public function show(int $comment): CommentResource
    {
        return new CommentResource(Comment::findOrFail($comment));
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
        $comment = Comment::findOrFail($id);

        throw_if((int) $data['user_id'] !== $comment->user_id, AuthorizationException::class);

        $comment->update([
            'comment' => $data['comment']
        ]);

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
        $hasLiked = CommentLike::whereUserId($user)->whereCommentId($id)->exists();

        throw_if($hasLiked, UserAlreadyLikedCommentException::class);

        $comment = Comment::findOrFail($id);

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
        $comment = Comment::findOrFail($id);
        $comment->delete();
    }
}
