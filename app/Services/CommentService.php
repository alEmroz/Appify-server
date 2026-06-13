<?php

namespace App\Services;

use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Illuminate\Contracts\Pagination\CursorPaginator;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Str;

class CommentService
{
    private const int DEFAULT_LIMIT = 15;

    private const array COMMENT_COLUMNS = ['id', 'uuid', 'user_id', 'post_id', 'parent_id', 'text', 'created_at'];

    private const array USER_COLUMNS = ['id', 'uuid', 'first_name', 'last_name'];

    public function listByPost(User $user, Post $post, ?string $cursor = null, int $limit = self::DEFAULT_LIMIT): CursorPaginator
    {
        return $post->comments()
            ->select(self::COMMENT_COLUMNS)
            ->whereNull('parent_id')
            ->with([
                'user' => fn ($q) => $q->select(self::USER_COLUMNS),
                'replies' => fn ($q) => $q->select(self::COMMENT_COLUMNS)
                    ->with(['user' => fn ($q) => $q->select(self::USER_COLUMNS)])
                    ->withCount('likes')
                    ->withExists(['likes as is_liked' => fn ($q) => $q->where('user_id', $user->id)])
                    ->orderBy('created_at'),
            ])
            ->withCount('likes')
            ->withExists(['likes as is_liked' => fn ($q) => $q->where('user_id', $user->id)])
            ->orderBy('created_at', 'desc')
            ->cursorPaginate($limit);
    }

    public function create(User $user, Post $post, array $data): Comment
    {
        $comment = Comment::create([
            'uuid' => (string) Str::uuid(),
            'user_id' => $user->id,
            'post_id' => $post->id,
            'text' => $data['text'],
        ]);

        $comment->load(['user' => fn ($q) => $q->select(self::USER_COLUMNS)]);
        $comment->loadCount('likes');
        $comment->loadExists(['likes as is_liked' => fn ($q) => $q->where('user_id', $user->id)]);

        return $comment;
    }

    public function reply(User $user, Comment $comment, array $data): Comment
    {
        if ($comment->parent_id !== null) {
            throw new ModelNotFoundException('Cannot reply to a reply');
        }

        $reply = Comment::create([
            'uuid' => (string) Str::uuid(),
            'user_id' => $user->id,
            'post_id' => $comment->post_id,
            'parent_id' => $comment->id,
            'text' => $data['text'],
        ]);

        $reply->load(['user' => fn ($q) => $q->select(self::USER_COLUMNS)]);
        $reply->loadCount('likes');
        $reply->loadExists(['likes as is_liked' => fn ($q) => $q->where('user_id', $user->id)]);

        return $reply;
    }

    public function delete(User $user, Comment $comment): void
    {
        if ($comment->user_id !== $user->id) {
            throw new ModelNotFoundException();
        }

        $comment->delete();
    }
}
