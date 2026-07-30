<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mail\ContactFormMail;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Throwable;

/**
 * @OA\Tags(
 *     name="Contact",
 *     description="Website contact form"
 * )
 */
class ContactController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/contact",
     *     summary="Send a contact form enquiry",
     *     tags={"Contact"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name","email","message"},
     *             @OA\Property(property="name", type="string", example="Jane Doe"),
     *             @OA\Property(property="company_name", type="string", example="Acme Pvt Ltd"),
     *             @OA\Property(property="email", type="string", format="email", example="jane@acme.com"),
     *             @OA\Property(property="mobile", type="string", example="+91 98765 43210"),
     *             @OA\Property(property="industry", type="string", example="Healthcare"),
     *             @OA\Property(property="message", type="string", example="We would like a quote.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Enquiry sent successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Thanks for reaching out! We'll get back to you shortly.")
     *         )
     *     ),
     *     @OA\Response(response=422, description="Validation failed"),
     *     @OA\Response(response=429, description="Too many requests"),
     *     @OA\Response(response=500, description="Enquiry could not be sent")
     * )
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name'         => 'required|string|max:255',
            'company_name' => 'nullable|string|max:255',
            'email'        => 'required|email|max:255',
            'mobile'       => 'nullable|string|max:30',
            'industry'     => 'nullable|string|max:255',
            'message'      => 'required|string|max:5000',
        ]);

        $recipient = config('mail.admin_address') ?: config('mail.from.address');

        try {
            Mail::to($recipient)->send(new ContactFormMail($validated));
        } catch (Throwable $e) {
            // Nothing is persisted, so a failed send loses the enquiry outright.
            // Surface it instead of reporting a success the visitor can't rely on.
            Log::error('Contact form mail failed', [
                'email' => $validated['email'],
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Sorry, your message could not be sent. Please try again or email us directly.',
            ], 500);
        }

        return response()->json([
            'success' => true,
            'message' => "Thanks for reaching out! We'll get back to you shortly.",
        ]);
    }
}
