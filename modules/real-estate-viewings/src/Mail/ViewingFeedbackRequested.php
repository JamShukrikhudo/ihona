<?php

declare(strict_types=1);

namespace Liberu\RealEstate\Viewings\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Liberu\RealEstate\Viewings\Models\ViewingFeedback;

final class ViewingFeedbackRequested extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public ViewingFeedback $feedback) {}

    public function build(): self
    {
        return $this->subject('Viewing feedback')->text('emails.viewing-feedback-requested');
    }
}
