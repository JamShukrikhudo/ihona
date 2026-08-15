<?php

namespace App\Mail;

use App\Models\ContactMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * The contact form tells a visitor "We reply within one working day". Before
 * this, every enquiry landed in a table nothing read — no resource, no
 * notification, no export. The promise had nothing behind it.
 */
class EnquiryReceived extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public ContactMessage $enquiry) {}

    public function envelope(): Envelope
    {
        $about = $this->enquiry->property?->title;

        return new Envelope(
            subject: $about
                ? __('Enquiry about :property', ['property' => $about])
                : __('Website enquiry from :name', ['name' => $this->enquiry->name]),
            replyTo: [$this->enquiry->email],
        );
    }

    public function content(): Content
    {
        return new Content(markdown: 'mail.enquiry-received');
    }
}
