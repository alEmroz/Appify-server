<?php

namespace App\Services;

use App\Models\Media;
use App\Models\Post;
use App\Models\User;
use Illuminate\Contracts\Pagination\CursorPaginator;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PostService
{
    private const int DEFAULT_LIMIT = 15;

    private const array POST_COLUMNS = ['id', 'uuid', 'user_id', 'text', 'visibility', 'created_at'];

    private const array USER_COLUMNS = ['id', 'uuid', 'first_name', 'last_name'];

    private const array MEDIA_COLUMNS = ['id', 'uuid', 'post_id', 'path', 'sort_order'];

    public function list(User $user, ?string $cursor = null, int $limit = self::DEFAULT_LIMIT): CursorPaginator
    {
        return Post::select(self::POST_COLUMNS)
            ->with([
                'user' => fn ($q) => $q->select(self::USER_COLUMNS),
                'media' => fn ($q) => $q->select(self::MEDIA_COLUMNS),
            ])
            ->where(function ($query) use ($user) {
                $query->where('visibility', 'public')
                    ->orWhere(function ($q) use ($user) {
                        $q->where('visibility', 'private')
                            ->where('user_id', $user->id);
                    });
            })
            ->withCount([
                'likes',
                'comments',
            ])
            ->withExists([
                'likes as is_liked' => fn ($q) => $q->where('user_id', $user->id),
            ])
            ->orderBy('created_at', 'desc')
            ->orderBy('id', 'desc')
            ->cursorPaginate($limit);
    }

    public function create(User $user, array $data, ?UploadedFile $image = null): Post
    {
        return DB::transaction(function () use ($user, $data, $image) {
            $post = Post::create([
                'uuid' => (string) Str::uuid(),
                'user_id' => $user->id,
                'text' => $data['text'],
                'visibility' => $data['visibility'] ?? 'public',
            ]);

            if (!$image) return $this->show($user, $post);

            $path = $image->store('posts', 'public');

            Media::create([
                'uuid' => (string) Str::uuid(),
                'post_id' => $post->id,
                'path' => $path,
            ]);

            return $this->show($user, $post);
        });
    }

    public function update(User $user, Post $post, array $data, ?UploadedFile $image = null): Post
    {
        if ($post->user_id !== $user->id) {
            throw new ModelNotFoundException();
        }

        return DB::transaction(function () use ($user, $post, $data, $image) {
            $post->update(Arr::only($data, ['text', 'visibility']));

            if ($image) {
                if ($post->media) {
                    Storage::disk('public')->delete($post->media->path);
                    $post->media->delete();
                }

                $path = $image->store('posts', 'public');

                Media::create([
                    'uuid' => (string) Str::uuid(),
                    'post_id' => $post->id,
                    'path' => $path,
                ]);
            }

            return $this->show($user, $post);
        });
    }

    public function show(User $user, Post $post): Post
    {
        if ($post->visibility === 'private' && $post->user_id !== $user->id) {
            throw new ModelNotFoundException();
        }

        $post->load(['user' => fn ($q) => $q->select(self::USER_COLUMNS)]);
        $post->load(['media' => fn ($q) => $q->select(self::MEDIA_COLUMNS)]);
        $post->loadCount(['likes', 'comments']);
        $post->loadExists(['likes as is_liked' => fn ($q) => $q->where('user_id', $user->id)]);

        return $post;
    }

    public function delete(User $user, Post $post): void
    {
        if ($post->user_id !== $user->id) {
            throw new ModelNotFoundException();
        }

        if ($post->media) {
            Storage::disk('public')->delete($post->media->path);
        }

        $post->delete();
    }
}
