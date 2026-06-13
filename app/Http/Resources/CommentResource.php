<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CommentResource extends JsonResource
{
    public static $wrap = null;

    public function toArray(Request $request): array
    {
        return [
            'uuid' => $this->uuid,
            'text' => $this->text,
            'user' => new UserResource($this->whenLoaded('user')),
            'likes_count' => (int) $this->likes_count,
            'is_liked' => (bool) ($this->is_liked ?? false),
            'created_at' => $this->created_at,
            'replies' => CommentResource::collection($this->whenLoaded('replies')),
        ];
    }
}
