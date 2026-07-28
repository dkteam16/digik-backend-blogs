<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CommentResource;
use App\Models\Comment;
use App\Models\Post;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * @OA\Tags(
 *     name="Comments",
 *     description="Comment endpoints"
 * )
 */
class CommentController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/posts/{slug}/comments",
     *     summary="List approved comments for a post",
     *     tags={"Comments"},
     *     @OA\Parameter(name="slug", in="path", required=true, @OA\Schema(type="string"), description="Post slug"),
     *     @OA\Response(
     *         response=200,
     *         description="Success",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="array", @OA\Items(type="object")),
     *             @OA\Property(property="meta", type="object")
     *         )
     *     ),
     *     @OA\Response(response=404, description="Post not found")
     * )
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
     * @OA\Post(
     *     path="/api/posts/{slug}/comments",
     *     summary="Submit a comment on a post",
     *     tags={"Comments"},
     *     @OA\Parameter(name="slug", in="path", required=true, @OA\Schema(type="string"), description="Post slug"),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"author_name","author_email","content"},
     *             @OA\Property(property="author_name", type="string", example="John Doe"),
     *             @OA\Property(property="author_email", type="string", format="email", example="john@example.com"),
     *             @OA\Property(property="content", type="string", minLength=3, maxLength=2000, example="Great post!"),
     *             @OA\Property(property="parent_id", type="integer", description="Parent comment ID for replies")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Comment submitted and awaiting moderation",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Comment submitted and awaiting moderation."),
     *             @OA\Property(property="data", type="object")
     *         )
     *     ),
     *     @OA\Response(response=404, description="Post not found or comments disabled"),
     *     @OA\Response(response=422, description="Validation failed")
     * )
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
            'is_approved' => false,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Comment submitted and awaiting moderation.',
            'data'    => new CommentResource($comment),
        ], 201);
    }
}
