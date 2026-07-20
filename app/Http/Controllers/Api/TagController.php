<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\TagResource;
use App\Models\Tag;
use Illuminate\Http\JsonResponse;

class TagController extends Controller
{
    /**
     * GET /api/tags
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
     * GET /api/tags/{slug}
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
