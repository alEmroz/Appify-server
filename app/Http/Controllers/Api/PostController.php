<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePostRequest;
use App\Http\Requests\UpdatePostRequest;
use App\Http\Resources\PostResource;
use App\Http\Resources\UserResource;
use App\Models\Post;
use App\Services\LikeService;
use App\Services\PostService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PostController extends Controller
{
    public function __construct(
        private readonly PostService $postService,
        private readonly LikeService $likeService,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $posts = $this->postService->list(
            $request->user(),
            $request->query('cursor'),
        );

        $postsResource = PostResource::collection($posts);

        return $postsResource->response();
    }

    public function store(StorePostRequest $request): JsonResponse
    {
        $post = $this->postService->create(
            $request->user(),
            $request->safe()->except('image'),
            $request->file('image'),
        );

        $postResource = new PostResource($post);

        return response()->json($postResource, Response::HTTP_CREATED);
    }

    public function show(Request $request, Post $post): JsonResponse
    {
        $post = $this->postService->show($request->user(), $post);

        $postResource = new PostResource($post);

        return response()->json($postResource);
    }

    public function update(UpdatePostRequest $request, Post $post): JsonResponse
    {
        $post = $this->postService->update(
            $request->user(),
            $post,
            $request->safe()->except('image'),
            $request->file('image'),
        );

        $postResource = new PostResource($post);

        return response()->json($postResource);
    }

    public function destroy(Request $request, Post $post): JsonResponse
    {
        $this->postService->delete($request->user(), $post);

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }

    public function like(Request $request, Post $post): JsonResponse
    {
        $result = $this->likeService->toggle($request->user(), $post);

        return response()->json($result, Response::HTTP_OK);
    }

    public function likes(Request $request, Post $post): JsonResponse
    {
        $paginator = $this->likeService->likers($post);

        $users = collect($paginator->items())
            ->map(fn ($like) => new UserResource($like->user));

        return response()->json([
            'data' => $users,
            'meta' => [
                'next_cursor' => $paginator->nextCursor()?->encode(),
                'prev_cursor' => $paginator->previousCursor()?->encode(),
                'per_page' => $paginator->perPage(),
            ],
        ]);
    }
}
