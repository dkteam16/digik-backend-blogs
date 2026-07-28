<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PostResource;
use App\Models\Post;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

/**
 * @OA\Tags(
 *     name="Posts",
 *     description="Blog post endpoints"
 * )
 */
class PostController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/posts",
     *     summary="List all published posts",
     *     tags={"Posts"},
     *     @OA\Parameter(name="category", in="query", @OA\Schema(type="string"), description="Filter by category slug"),
     *     @OA\Parameter(name="tag", in="query", @OA\Schema(type="string"), description="Filter by tag slug"),
     *     @OA\Parameter(name="search", in="query", @OA\Schema(type="string"), description="Search in title, excerpt, content"),
     *     @OA\Parameter(name="featured", in="query", @OA\Schema(type="boolean"), description="Filter featured posts only"),
     *     @OA\Parameter(name="sort_by", in="query", @OA\Schema(type="string", enum={"created_at","views_count","published_at"}), description="Sort field"),
     *     @OA\Parameter(name="sort_dir", in="query", @OA\Schema(type="string", enum={"asc","desc"}), description="Sort direction"),
     *     @OA\Parameter(name="per_page", in="query", @OA\Schema(type="integer", maximum=50), description="Items per page"),
     *     @OA\Response(
     *         response=200,
     *         description="Success",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="array", @OA\Items(type="object")),
     *             @OA\Property(property="meta", type="object")
     *         )
     *     )
     * )
     */
    public function index(Request $request): JsonResponse
    {
        $query = Post::with(['author', 'category', 'tags'])
            ->published();

        if ($request->filled('category')) {
            $query->whereHas('category', fn ($q) => $q->where('slug', $request->category));
        }

        if ($request->filled('tag')) {
            $query->whereHas('tags', fn ($q) => $q->where('slug', $request->tag));
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('excerpt', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%");
            });
        }

        if ($request->boolean('featured')) {
            $query->featured();
        }

        $sortBy  = in_array($request->sort_by, ['created_at', 'views_count', 'published_at'])
            ? $request->sort_by : 'published_at';
        $sortDir = $request->sort_dir === 'asc' ? 'asc' : 'desc';
        $query->orderBy($sortBy, $sortDir);

        $perPage = min((int) $request->get('per_page', 10), 50);
        $posts   = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data'    => PostResource::collection($posts),
            'meta'    => [
                'current_page' => $posts->currentPage(),
                'last_page'    => $posts->lastPage(),
                'per_page'     => $posts->perPage(),
                'total'        => $posts->total(),
            ],
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/posts/{slug}",
     *     summary="Get a single post by slug",
     *     tags={"Posts"},
     *     @OA\Parameter(name="slug", in="path", required=true, @OA\Schema(type="string"), description="Post slug"),
     *     @OA\Response(
     *         response=200,
     *         description="Success",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="object")
     *         )
     *     ),
     *     @OA\Response(response=404, description="Post not found")
     * )
     */
    public function show(string $slug): JsonResponse
    {
        $post = Post::with(['author', 'category', 'tags', 'approvedComments.replies'])
            ->published()
            ->where('slug', $slug)
            ->firstOrFail();

        $post->incrementViews();

        return response()->json([
            'success' => true,
            'data'    => new PostResource($post),
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/posts/featured",
     *     summary="Get featured posts",
     *     tags={"Posts"},
     *     @OA\Response(
     *         response=200,
     *         description="Success",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="array", @OA\Items(type="object"))
     *         )
     *     )
     * )
     */
    public function featured(): JsonResponse
    {
        $posts = Post::with(['author', 'category', 'tags'])
            ->published()
            ->featured()
            ->orderBy('published_at', 'desc')
            ->limit(6)
            ->get();

        return response()->json([
            'success' => true,
            'data'    => PostResource::collection($posts),
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/posts/{slug}/related",
     *     summary="Get related posts",
     *     tags={"Posts"},
     *     @OA\Parameter(name="slug", in="path", required=true, @OA\Schema(type="string"), description="Post slug"),
     *     @OA\Response(
     *         response=200,
     *         description="Success",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="array", @OA\Items(type="object"))
     *         )
     *     ),
     *     @OA\Response(response=404, description="Post not found")
     * )
     */
    public function related(string $slug): JsonResponse
    {
        $post = Post::published()->where('slug', $slug)->firstOrFail();

        $related = Post::with(['author', 'category'])
            ->published()
            ->where('id', '!=', $post->id)
            ->where('category_id', $post->category_id)
            ->orderBy('published_at', 'desc')
            ->limit(4)
            ->get();

        return response()->json([
            'success' => true,
            'data'    => PostResource::collection($related),
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/posts",
     *     summary="Create a new post",
     *     tags={"Posts"},
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"title","content"},
     *                 @OA\Property(property="title", type="string", example="My New Post"),
     *                 @OA\Property(property="excerpt", type="string", example="Post excerpt"),
     *                 @OA\Property(property="content", type="string", example="Post content here"),
     *                 @OA\Property(property="category_id", type="integer", example=1),
     *                 @OA\Property(property="tags", type="array", @OA\Items(type="integer"), example={1,2}),
     *                 @OA\Property(property="status", type="string", enum={"draft","published","scheduled"}, example="draft"),
     *                 @OA\Property(property="is_featured", type="boolean", example=false),
     *                 @OA\Property(property="allow_comments", type="boolean", example=true),
     *                 @OA\Property(property="featured_image", type="string", format="binary"),
     *                 @OA\Property(property="meta_title", type="string"),
     *                 @OA\Property(property="meta_description", type="string"),
     *                 @OA\Property(property="meta_keywords", type="string"),
     *                 @OA\Property(property="published_at", type="string", format="date-time")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Post created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Post created successfully"),
     *             @OA\Property(property="data", type="object")
     *         )
     *     ),
     *     @OA\Response(response=401, description="Unauthenticated"),
     *     @OA\Response(response=422, description="Validation failed")
     * )
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title'            => 'required|string|max:255',
            'excerpt'          => 'nullable|string|max:500',
            'content'          => 'required|string',
            'category_id'      => 'nullable|exists:categories,id',
            'tags'             => 'nullable|array',
            'tags.*'           => 'exists:tags,id',
            'status'           => 'in:draft,published,scheduled',
            'is_featured'      => 'boolean',
            'allow_comments'   => 'boolean',
            'meta_title'       => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'meta_keywords'    => 'nullable|string|max:255',
            'published_at'     => 'nullable|date',
        ]);

        $validated['slug']      = Str::slug($validated['title']);
        $validated['author_id'] = $request->user()->id;

        if (($validated['status'] ?? 'draft') === 'published' && empty($validated['published_at'])) {
            $validated['published_at'] = now();
        }

        $post = Post::create($validated);

        if (!empty($validated['tags'])) {
            $post->tags()->sync($validated['tags']);
        }

        return response()->json([
            'success' => true,
            'message' => 'Post created successfully',
            'data'    => new PostResource($post->load(['author', 'category', 'tags'])),
        ], 201);
    }

    /**
     * @OA\Put(
     *     path="/api/posts/{id}",
     *     summary="Update a post",
     *     tags={"Posts"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer"), description="Post ID"),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property(property="title", type="string", example="Updated Post Title"),
     *                 @OA\Property(property="excerpt", type="string"),
     *                 @OA\Property(property="content", type="string"),
     *                 @OA\Property(property="category_id", type="integer"),
     *                 @OA\Property(property="tags", type="array", @OA\Items(type="integer")),
     *                 @OA\Property(property="status", type="string", enum={"draft","published","scheduled","archived"}),
     *                 @OA\Property(property="is_featured", type="boolean"),
     *                 @OA\Property(property="allow_comments", type="boolean"),
     *                 @OA\Property(property="featured_image", type="string", format="binary"),
     *                 @OA\Property(property="meta_title", type="string"),
     *                 @OA\Property(property="meta_description", type="string"),
     *                 @OA\Property(property="meta_keywords", type="string"),
     *                 @OA\Property(property="published_at", type="string", format="date-time")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Post updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Post updated successfully"),
     *             @OA\Property(property="data", type="object")
     *         )
     *     ),
     *     @OA\Response(response=401, description="Unauthenticated"),
     *     @OA\Response(response=403, description="Forbidden"),
     *     @OA\Response(response=404, description="Post not found"),
     *     @OA\Response(response=422, description="Validation failed")
     * )
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $post = Post::findOrFail($id);

        $user = $request->user();
        if (!$user->isEditor() && $post->author_id !== $user->id) {
            return response()->json(['success' => false, 'message' => 'Forbidden'], 403);
        }

        $validated = $request->validate([
            'title'            => 'sometimes|string|max:255',
            'excerpt'          => 'nullable|string|max:500',
            'content'          => 'sometimes|string',
            'category_id'      => 'nullable|exists:categories,id',
            'tags'             => 'nullable|array',
            'tags.*'           => 'exists:tags,id',
            'status'           => 'sometimes|in:draft,published,scheduled,archived',
            'is_featured'      => 'boolean',
            'allow_comments'   => 'boolean',
            'meta_title'       => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'meta_keywords'    => 'nullable|string|max:255',
            'published_at'     => 'nullable|date',
        ]);

        if (isset($validated['title'])) {
            $validated['slug'] = Str::slug($validated['title']);
        }

        $post->update($validated);

        if (array_key_exists('tags', $validated)) {
            $post->tags()->sync($validated['tags'] ?? []);
        }

        return response()->json([
            'success' => true,
            'message' => 'Post updated successfully',
            'data'    => new PostResource($post->fresh()->load(['author', 'category', 'tags'])),
        ]);
    }

    /**
     * @OA\Delete(
     *     path="/api/posts/{id}",
     *     summary="Delete a post",
     *     tags={"Posts"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer"), description="Post ID"),
     *     @OA\Response(
     *         response=200,
     *         description="Post deleted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Post deleted successfully")
     *         )
     *     ),
     *     @OA\Response(response=401, description="Unauthenticated"),
     *     @OA\Response(response=403, description="Forbidden"),
     *     @OA\Response(response=404, description="Post not found")
     * )
     */
    public function destroy(Request $request, int $id): JsonResponse
    {
        $post = Post::findOrFail($id);

        $user = $request->user();
        if (!$user->isEditor() && $post->author_id !== $user->id) {
            return response()->json(['success' => false, 'message' => 'Forbidden'], 403);
        }

        $post->delete();

        return response()->json([
            'success' => true,
            'message' => 'Post deleted successfully',
        ]);
    }
}
