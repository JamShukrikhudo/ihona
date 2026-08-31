<?php

declare(strict_types=1);

namespace Liberu\RealEstate\Properties\Mail;

use Illuminate\Mail\Mailable;

final class PropertyShareMail extends Mailable
{
    /** @param array<string, mixed> $data */
    public function __construct(private readonly array $data) {}

    public function build(): self
    {
        return $this->to($this->data['recipient_email'], $this->data['recipient_name'])
            ->replyTo($this->data['sender_email'], $this->data['sender_name'])
            ->subject($this->data['subject'])
            ->html($this->data['body']);
    }
}
