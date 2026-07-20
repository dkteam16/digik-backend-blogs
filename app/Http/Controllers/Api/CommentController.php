<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CommentResource;
use App\Models\Comment;
use App\Models\Post;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    /**
     * GET /api/posts/{slug}/comments
     */
    public function index(string $slug): JsonResponse
    {
        $post = Post::published()->where('slug', $slug)->firstOrFail();

        $comments = Comment::with('replies')
            ->where('post_id', $post->id)
            ->where('is_approved', true)
            ->whereNull('parent_id')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json([
            'success' => true,
            'data'    => CommentResource::collection($comments),
            'meta'    => [
                'total'        => $comments->total(),
                'current_page' => $comments->currentPage(),
                'last_page'    => $comments->lastPage(),
            ],
        ]);
    }

    /**
     * POST /api/posts/{slug}/comments
     */
    public function store(Request $request, string $slug): JsonResponse
    {
        $post = Post::published()
            ->where('slug', $slug)
            ->where('allow_comments', true)
            ->firstOrFail();

        $validated = $request->validate([
            'author_name'  => 'required|string|max:100',
            'author_email' => 'required|email',
            'content'      => 'required|string|min:3|max:2000',
            'parent_id'    => 'nullable|exists:comments,id',
        ]);

        $comment = Comment::create([
            ...$validated,
            'post_id'     => $post->id,
            'user_id'     => $request->user()?->id,
            'is_approved' => false, // always requires moderation
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Comment submitted and awaiting moderation.',
            'data'    => new CommentResource($comment),
        ], 201);
    }
}
