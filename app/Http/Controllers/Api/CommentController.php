<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCommentRequest;
use App\Http\Requests\UpdateCommentRequest;
use App\Http\Resources\CommentResource;
use App\Http\Resources\UserResource;
use App\Models\Comment;
use App\Models\Post;
use App\Services\CommentService;
use App\Services\LikeService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CommentController extends Controller
{
    public function __construct(
        private readonly CommentService $commentService,
        private readonly LikeService $likeService,
    ) {}

    public function index(Request $request, Post $post): JsonResponse
    {
        $comments = $this->commentService->listByPost(
            $request->user(),
            $post,
            $request->query('cursor'),
        );

        $commentsResource = CommentResource::collection($comments);

        return $commentsResource->response();
    }

    public function store(StoreCommentRequest $request, Post $post): JsonResponse
    {
        $comment = $this->commentService->create(
            $request->user(),
            $post,
            $request->validated(),
        );

        $commentResource = new CommentResource($comment);

        return response()->json($commentResource, Response::HTTP_CREATED);
    }

    public function update(UpdateCommentRequest $request, Comment $comment): JsonResponse
    {
        $comment = $this->commentService->update(
            $request->user(),
            $comment,
            $request->validated(),
        );

        return response()->json(new CommentResource($comment));
    }

    public function reply(StoreCommentRequest $request, Comment $comment): JsonResponse
    {
        $reply = $this->commentService->reply(
            $request->user(),
            $comment,
            $request->validated(),
        );

        $replyResource = new CommentResource($reply);

        return response()->json($replyResource, Response::HTTP_CREATED);
    }

    public function destroy(Request $request, Comment $comment): JsonResponse
    {
        $this->commentService->delete($request->user(), $comment);

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }

    public function like(Request $request, Comment $comment): JsonResponse
    {
        $result = $this->likeService->toggle($request->user(), $comment);

        return response()->json($result, Response::HTTP_OK);
    }

    public function likes(Request $request, Comment $comment): JsonResponse
    {
        $paginator = $this->likeService->likers($comment);

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
