<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\NewsletterSubscriber;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * @OA\Tags(
 *     name="Newsletter",
 *     description="Newsletter subscription endpoint"
 * )
 */
class NewsletterSubscriberController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/newsletter/subscribe",
     *     summary="Subscribe an email address to the newsletter",
     *     tags={"Newsletter"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email"},
     *             @OA\Property(property="email", type="string", format="email", example="jane@example.com")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Subscribed successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Subscribed successfully")
     *         )
     *     ),
     *     @OA\Response(response=422, description="Validation failed")
     * )
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => 'required|email|max:255|unique:newsletter_subscribers,email',
        ]);

        NewsletterSubscriber::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Subscribed successfully',
        ], 201);
    }
}
