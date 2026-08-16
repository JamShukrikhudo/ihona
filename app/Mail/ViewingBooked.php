<?php

namespace App\Mail;

use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * Confirms a viewing to whoever booked it.
 *
 * The notification that existed only fired for a logged-in user, so a guest —
 * the majority of people booking a first viewing — got a message on screen and
 * nothing they could keep.
 */
class ViewingBooked extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Booking $booking) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: __('Viewing booked — :property', [
                'property' => $this->booking->property?->title ?? __('your viewing'),
            ]),
        );
    }

    public function content(): Content
    {
        return new Content(markdown: 'mail.viewing-booked');
    }
}
