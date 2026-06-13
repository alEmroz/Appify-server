<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PostResource extends JsonResource
{
    public static $wrap = null;

    public function toArray(Request $request): array
    {
        return [
            'uuid' => $this->uuid,
            'text' => $this->text,
            'visibility' => $this->visibility,
            'user' => new UserResource($this->whenLoaded('user')),
            'media' => $this->whenLoaded('media', fn () => $this->media ? new MediaResource($this->media) : null),
            'likes_count' => (int) $this->likes_count,
            'comments_count' => (int) $this->comments_count,
            'is_liked' => (bool) ($this->is_liked ?? false),
            'created_at' => $this->created_at,
        ];
    }
}
