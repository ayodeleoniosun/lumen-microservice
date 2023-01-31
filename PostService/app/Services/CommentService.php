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

class CommentService implements CommentServiceInterface
{
    /**
     * Get all comments
     *
     * @return CommentCollection
     */
    public function index(int $post): CommentCollection
    {
        return new CommentCollection(Comment::where('post_id', $post)->with('likes')->get());
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
     */
    public function update(array $data, int $id): Model
    {
        $comment = Comment::findOrFail($id);

        if ((int) $data['user_id'] !== $comment->user_id) {
            throw new AuthorizationException();
        }

        $comment->comment = $data['comment'];
        $comment->save();

        return $comment;
    }

    /**
     * @param int $user
     * @param int $id
     * @return Model
     * @throws UserAlreadyLikedCommentException
     */
    public function like(int $user, int $id): Model
    {
        $hasLiked = CommentLike::where([
            'user_id' => $user,
            'comment_id' => $id,
        ])->exists();

        if ($hasLiked) {
            throw new UserAlreadyLikedCommentException();
        }

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
