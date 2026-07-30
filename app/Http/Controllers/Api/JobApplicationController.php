<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Mail\JobApplicationMail;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Throwable;

/**
 * @OA\Tags(
 *     name="Careers",
 *     description="Job application form"
 * )
 */
class JobApplicationController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/careers/apply",
     *     summary="Submit a job application with a CV",
     *     tags={"Careers"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"name","email","cv"},
     *                 @OA\Property(property="name", type="string", example="Jane Doe"),
     *                 @OA\Property(property="email", type="string", format="email", example="jane@example.com"),
     *                 @OA\Property(property="mobile", type="string", example="+91 98765 43210"),
     *                 @OA\Property(property="area_of_interest", type="string", example="Backend Development"),
     *                 @OA\Property(property="cv", type="string", format="binary", description="PDF/DOC/DOCX, max 2MB")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Application sent successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Thanks for applying! We'll be in touch soon.")
     *         )
     *     ),
     *     @OA\Response(response=422, description="Validation failed"),
     *     @OA\Response(response=429, description="Too many requests"),
     *     @OA\Response(response=500, description="Application could not be sent")
     * )
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name'             => 'required|string|max:255',
            'email'            => 'required|email|max:255',
            'mobile'           => 'nullable|string|max:30',
            'area_of_interest' => 'nullable|string|max:255',
            // Capped at 2MB to match PHP's upload_max_filesize; raising this rule
            // alone will not work, the ini values have to go up too.
            'cv'               => 'required|file|mimes:pdf,doc,docx|max:2048',
        ], [
            // A file over upload_max_filesize is dropped by PHP before the `max`
            // rule runs, and the default wording for that ("failed to upload")
            // tells the applicant nothing. Spell out the real cause.
            'cv.uploaded' => 'The CV could not be uploaded. It must be smaller than 2 MB.',
            'cv.max'      => 'The CV must not be larger than 2 MB.',
            'cv.mimes'    => 'The CV must be a PDF, DOC or DOCX file.',
            'cv.required' => 'Please attach your CV.',
        ]);

        $file = $request->file('cv');

        $cv = [
            'filename' => $file->getClientOriginalName(),
            'mime'     => $file->getMimeType(),
            'contents' => file_get_contents($file->getRealPath()),
        ];

        unset($validated['cv']);

        $recipient = config('mail.admin_address') ?: config('mail.from.address');

        try {
            Mail::to($recipient)->send(new JobApplicationMail($validated, $cv));
        } catch (Throwable $e) {
            // Nothing is persisted and the upload is discarded with the request,
            // so a failed send loses the application and its CV outright.
            Log::error('Job application mail failed', [
                'email' => $validated['email'],
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Sorry, your application could not be sent. Please try again or email us directly.',
            ], 500);
        }

        return response()->json([
            'success' => true,
            'message' => "Thanks for applying! We'll be in touch soon.",
        ]);
    }
}
