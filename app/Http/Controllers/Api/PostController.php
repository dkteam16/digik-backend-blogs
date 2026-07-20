<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\PostResource;
use App\Models\Post;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PostController extends Controller
{
    /**
     * GET /api/posts
     */
    public function index(Request $request): JsonResponse
    {
        $query = Post::with(['author', 'category', 'tags'])
            ->published();

        // Filter by category slug
        if ($request->filled('category')) {
            $query->whereHas('category', fn ($q) => $q->where('slug', $request->category));
        }

        // Filter by tag slug
        if ($request->filled('tag')) {
            $query->whereHas('tags', fn ($q) => $q->where('slug', $request->tag));
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('excerpt', 'like', "%{$search}%")
                  ->orWhere('content', 'like', "%{$search}%");
            });
        }

        // Featured only
        if ($request->boolean('featured')) {
            $query->featured();
        }

        // Sort
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
     * GET /api/posts/{slug}
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
     * GET /api/posts/featured
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
     * GET /api/posts/related/{slug}
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
     * POST /api/posts (Auth required)
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
     * PUT /api/posts/{id} (Auth required)
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $post = Post::findOrFail($id);

        // Only admin/editor or the author can update
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
     * DELETE /api/posts/{id} (Auth required)
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
