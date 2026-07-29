<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\HiringPostResource;
use App\Models\HiringPost;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

/**
 * @OA\Tags(
 *     name="Hiring Posts",
 *     description="Job listing endpoints"
 * )
 */
class HiringPostController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/hiring-posts",
     *     summary="List all published job listings",
     *     tags={"Hiring Posts"},
     *     @OA\Parameter(name="department", in="query", @OA\Schema(type="string"), description="Filter by department"),
     *     @OA\Parameter(name="work_type", in="query", @OA\Schema(type="string", enum={"onsite","remote","hybrid"}), description="Filter by work type"),
     *     @OA\Parameter(name="employment_type", in="query", @OA\Schema(type="string", enum={"full-time","part-time","contract","internship"}), description="Filter by employment type"),
     *     @OA\Parameter(name="search", in="query", @OA\Schema(type="string"), description="Search in title, description"),
     *     @OA\Parameter(name="featured", in="query", @OA\Schema(type="boolean"), description="Filter featured listings only"),
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
        $query = HiringPost::with('author')
            ->published()
            ->open();

        if ($request->filled('department')) {
            $query->where('department', $request->department);
        }

        if ($request->filled('work_type')) {
            $query->where('work_type', $request->work_type);
        }

        if ($request->filled('employment_type')) {
            $query->where('employment_type', $request->employment_type);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->boolean('featured')) {
            $query->featured();
        }

        $query->orderBy('published_at', 'desc');

        $perPage = min((int) $request->get('per_page', 10), 50);
        $posts   = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data'    => HiringPostResource::collection($posts),
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
     *     path="/api/hiring-posts/{slug}",
     *     summary="Get a single job listing by slug",
     *     tags={"Hiring Posts"},
     *     @OA\Parameter(name="slug", in="path", required=true, @OA\Schema(type="string"), description="Hiring post slug"),
     *     @OA\Response(
     *         response=200,
     *         description="Success",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="object")
     *         )
     *     ),
     *     @OA\Response(response=404, description="Hiring post not found")
     * )
     */
    public function show(string $slug): JsonResponse
    {
        $post = HiringPost::with('author')
            ->published()
            ->where('slug', $slug)
            ->firstOrFail();

        $post->incrementViews();

        return response()->json([
            'success' => true,
            'data'    => new HiringPostResource($post),
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/hiring-posts/featured",
     *     summary="Get featured job listings",
     *     tags={"Hiring Posts"},
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
        $posts = HiringPost::with('author')
            ->published()
            ->open()
            ->featured()
            ->orderBy('published_at', 'desc')
            ->limit(6)
            ->get();

        return response()->json([
            'success' => true,
            'data'    => HiringPostResource::collection($posts),
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/hiring-posts",
     *     summary="Create a new job listing",
     *     tags={"Hiring Posts"},
     *     security={{"sanctum":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"title","description"},
     *             @OA\Property(property="title", type="string", example="Senior Backend Engineer"),
     *             @OA\Property(property="description", type="string", example="Job description here"),
     *             @OA\Property(property="qualification", type="string"),
     *             @OA\Property(property="experience", type="string", example="2-4 years"),
     *             @OA\Property(property="department", type="string", example="Engineering"),
     *             @OA\Property(property="location", type="string", example="Remote"),
     *             @OA\Property(property="work_type", type="string", enum={"onsite","remote","hybrid"}),
     *             @OA\Property(property="employment_type", type="string", enum={"full-time","part-time","contract","internship"}),
     *             @OA\Property(property="apply_url", type="string", example="https://example.com/apply"),
     *             @OA\Property(property="status", type="string", enum={"draft","published","closed","archived"}, example="draft"),
     *             @OA\Property(property="is_featured", type="boolean", example=false),
     *             @OA\Property(property="application_deadline", type="string", format="date"),
     *             @OA\Property(property="meta_title", type="string"),
     *             @OA\Property(property="meta_description", type="string"),
     *             @OA\Property(property="meta_keywords", type="string"),
     *             @OA\Property(property="published_at", type="string", format="date-time")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Hiring post created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Hiring post created successfully"),
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
            'title'                 => 'required|string|max:255',
            'description'           => 'required|string',
            'qualification'         => 'nullable|string',
            'experience'            => 'nullable|string|max:255',
            'department'            => 'nullable|string|max:255',
            'location'              => 'nullable|string|max:255',
            'work_type'             => 'nullable|in:onsite,remote,hybrid',
            'employment_type'       => 'in:full-time,part-time,contract,internship',
            'apply_url'             => 'nullable|url|max:255',
            'status'                => 'in:draft,published,closed,archived',
            'is_featured'           => 'boolean',
            'application_deadline'  => 'nullable|date',
            'meta_title'            => 'nullable|string|max:255',
            'meta_description'      => 'nullable|string|max:500',
            'meta_keywords'         => 'nullable|string|max:255',
            'published_at'          => 'nullable|date',
        ]);

        $validated['slug']      = Str::slug($validated['title']);
        $validated['author_id'] = $request->user()->id;

        if (($validated['status'] ?? 'draft') === 'published' && empty($validated['published_at'])) {
            $validated['published_at'] = now();
        }

        $post = HiringPost::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Hiring post created successfully',
            'data'    => new HiringPostResource($post->load('author')),
        ], 201);
    }

    /**
     * @OA\Put(
     *     path="/api/hiring-posts/{id}",
     *     summary="Update a job listing",
     *     tags={"Hiring Posts"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer"), description="Hiring post ID"),
     *     @OA\Response(
     *         response=200,
     *         description="Hiring post updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Hiring post updated successfully"),
     *             @OA\Property(property="data", type="object")
     *         )
     *     ),
     *     @OA\Response(response=401, description="Unauthenticated"),
     *     @OA\Response(response=403, description="Forbidden"),
     *     @OA\Response(response=404, description="Hiring post not found"),
     *     @OA\Response(response=422, description="Validation failed")
     * )
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $post = HiringPost::findOrFail($id);

        $user = $request->user();
        if (!$user->isEditor() && $post->author_id !== $user->id) {
            return response()->json(['success' => false, 'message' => 'Forbidden'], 403);
        }

        $validated = $request->validate([
            'title'                 => 'sometimes|string|max:255',
            'description'           => 'sometimes|string',
            'qualification'         => 'nullable|string',
            'experience'            => 'nullable|string|max:255',
            'department'            => 'nullable|string|max:255',
            'location'              => 'nullable|string|max:255',
            'work_type'             => 'nullable|in:onsite,remote,hybrid',
            'employment_type'       => 'sometimes|in:full-time,part-time,contract,internship',
            'apply_url'             => 'nullable|url|max:255',
            'status'                => 'sometimes|in:draft,published,closed,archived',
            'is_featured'           => 'boolean',
            'application_deadline'  => 'nullable|date',
            'meta_title'            => 'nullable|string|max:255',
            'meta_description'      => 'nullable|string|max:500',
            'meta_keywords'         => 'nullable|string|max:255',
            'published_at'          => 'nullable|date',
        ]);

        if (isset($validated['title'])) {
            $validated['slug'] = Str::slug($validated['title']);
        }

        $post->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Hiring post updated successfully',
            'data'    => new HiringPostResource($post->fresh()->load('author')),
        ]);
    }

    /**
     * @OA\Delete(
     *     path="/api/hiring-posts/{id}",
     *     summary="Delete a job listing",
     *     tags={"Hiring Posts"},
     *     security={{"sanctum":{}}},
     *     @OA\Parameter(name="id", in="path", required=true, @OA\Schema(type="integer"), description="Hiring post ID"),
     *     @OA\Response(
     *         response=200,
     *         description="Hiring post deleted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Hiring post deleted successfully")
     *         )
     *     ),
     *     @OA\Response(response=401, description="Unauthenticated"),
     *     @OA\Response(response=403, description="Forbidden"),
     *     @OA\Response(response=404, description="Hiring post not found")
     * )
     */
    public function destroy(Request $request, int $id): JsonResponse
    {
        $post = HiringPost::findOrFail($id);

        $user = $request->user();
        if (!$user->isEditor() && $post->author_id !== $user->id) {
            return response()->json(['success' => false, 'message' => 'Forbidden'], 403);
        }

        $post->delete();

        return response()->json([
            'success' => true,
            'message' => 'Hiring post deleted successfully',
        ]);
    }
}
