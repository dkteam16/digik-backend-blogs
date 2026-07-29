<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mail\NewsletterSubscribedNotification;
use App\Mail\NewsletterWelcomeMail;
use App\Models\NewsletterSubscriber;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Throwable;

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

        $subscriber = NewsletterSubscriber::create($validated);

        $this->sendSubscriptionMail($subscriber);

        return response()->json([
            'success' => true,
            'message' => 'Subscribed successfully',
        ], 201);
    }

    /**
     * The subscriber row is already saved by this point, so a mail failure
     * (bad credentials, SMTP timeout) is logged rather than surfaced as a
     * failed subscription to the visitor.
     */
    private function sendSubscriptionMail(NewsletterSubscriber $subscriber): void
    {
        try {
            Mail::to($subscriber->email)->send(new NewsletterWelcomeMail($subscriber));

            if ($adminAddress = config('mail.admin_address')) {
                Mail::to($adminAddress)->send(new NewsletterSubscribedNotification($subscriber));
            }
        } catch (Throwable $e) {
            Log::error('Newsletter mail failed', [
                'email' => $subscriber->email,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
