<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class JobApplicationMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @param array{name:string,email:string,mobile:?string,area_of_interest:?string} $fields
     * @param array{filename:string,mime:string,contents:string}                      $cv
     */
    public function __construct(public array $fields, public array $cv)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Job application from ' . $this->fields['name'],
            // Gmail rewrites From: to the authenticated account, so Reply-To is
            // what lets "Reply" reach the applicant.
            replyTo: [new Address($this->fields['email'], $this->fields['name'])],
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.job-application',
        );
    }

    /**
     * Attached from raw bytes rather than a path: the uploaded file lives in a
     * temp location that is cleaned up when the request ends.
     */
    public function attachments(): array
    {
        return [
            Attachment::fromData(fn () => $this->cv['contents'], $this->cv['filename'])
                ->withMime($this->cv['mime']),
        ];
    }
}
