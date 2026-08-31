<?php

declare(strict_types=1);

namespace Liberu\RealEstate\PartiesLivewire\Components;

use Illuminate\Contracts\View\View;
use Liberu\RealEstate\Parties\Application\CreateContactMessage;
use Livewire\Attributes\Validate;
use Livewire\Component;

final class ContactEnquiryForm extends Component
{
    #[Validate('required|string|max:255')]
    public string $name = '';

    #[Validate('required|email:rfc|max:255')]
    public string $email = '';

    #[Validate('nullable|string|max:50')]
    public string $phone = '';

    #[Validate('nullable|in:buying,selling,renting,letting,other')]
    public string $interest = '';

    #[Validate('required|string|max:5000')]
    public string $message = '';

    public bool $submitted = false;

    public function submit(CreateContactMessage $createContactMessage): void
    {
        $this->validate();

        $createContactMessage->handle([
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone ?: null,
            'interest' => $this->interest ?: null,
            'message' => $this->message,
        ]);

        $this->reset('name', 'email', 'phone', 'interest', 'message');
        $this->submitted = true;
    }

    public function render(): View
    {
        return view('real-estate-parties-livewire::contact-enquiry');
    }
}
