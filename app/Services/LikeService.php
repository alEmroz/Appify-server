<?php

namespace App\Services;

use App\Http\Resources\UserResource;
use App\Models\Like;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class LikeService
{
    public function toggle(User $user, Model $likeable): array
    {
        $like = Like::where('user_id', $user->id)
            ->where('likeable_id', $likeable->id)
            ->where('likeable_type', $likeable->getMorphClass())
            ->first();

        if ($like) {
            $like->delete();
            $isLiked = false;
        } else {
            Like::create([
                'uuid' => (string) Str::uuid(),
                'user_id' => $user->id,
                'likeable_id' => $likeable->id,
                'likeable_type' => $likeable->getMorphClass(),
            ]);
            $isLiked = true;
        }

        $likesCount = $likeable->likes()->count();

        return [
            'is_liked' => $isLiked,
            'likes_count' => $likesCount,
        ];
    }

    public function likers(Model $likeable): array
    {
        $likers = $likeable->likes()
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->get()
            ->pluck('user');

        return [
            'data' => UserResource::collection($likers),
        ];
    }
}
