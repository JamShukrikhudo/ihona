<?php

declare(strict_types=1);

namespace Liberu\RealEstate\PartiesLivewire\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Liberu\RealEstate\Parties\Application\CreateContactMessage;

final class ContactEnquiryController
{
    public function store(Request $request, CreateContactMessage $createContactMessage): RedirectResponse
    {
        $createContactMessage->handle($request->all());

        return redirect()->route('contact.show')->with('status', 'Thank you. We will be in touch soon.');
    }
}
