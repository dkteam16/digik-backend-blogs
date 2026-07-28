<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\TagResource;
use App\Models\Tag;
use Illuminate\Http\JsonResponse;

/**
 * @OA\Tags(
 *     name="Tags",
 *     description="Tag endpoints"
 * )
 */
class TagController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/tags",
     *     summary="List all tags",
     *     tags={"Tags"},
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
    public function index(): JsonResponse
    {
        $tags = Tag::withCount('posts')
            ->orderByDesc('posts_count')
            ->get();

        return response()->json([
            'success' => true,
            'data'    => TagResource::collection($tags),
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/tags/{slug}",
     *     summary="Get a single tag by slug",
     *     tags={"Tags"},
     *     @OA\Parameter(name="slug", in="path", required=true, @OA\Schema(type="string"), description="Tag slug"),
     *     @OA\Response(
     *         response=200,
     *         description="Success",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="object")
     *         )
     *     ),
     *     @OA\Response(response=404, description="Tag not found")
     * )
     */
    public function show(string $slug): JsonResponse
    {
        $tag = Tag::withCount('posts')->where('slug', $slug)->firstOrFail();

        return response()->json([
            'success' => true,
            'data'    => new TagResource($tag),
        ]);
    }
}
