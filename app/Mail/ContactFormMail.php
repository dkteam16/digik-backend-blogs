<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ContactFormMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @param array{name:string,company_name:?string,email:string,mobile:?string,industry:?string,message:string} $fields
     */
    public function __construct(public array $fields)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'New contact enquiry from ' . $this->fields['name'],
            // Gmail forces the From: header to the authenticated account, so
            // Reply-To is what makes hitting "Reply" actually reach the enquirer.
            replyTo: [new Address($this->fields['email'], $this->fields['name'])],
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.contact-form',
        );
    }
}
