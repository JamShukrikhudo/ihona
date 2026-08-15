<?php

namespace App\Http\Controllers;

use App\Models\ContactMessage;
use App\Mail\EnquiryReceived;
use App\Models\Property;
use App\Settings\GeneralSettings;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    public function show(Request $request)
    {
        // A listing card's "Ask a question" arrives here with the property, so
        // the agent can tell which home the question is about.
        $property = $request->filled('property')
            ? Property::find($request->integer('property'))
            : null;

        return view('contact', ['property' => $property]);
    }

    public function submit(Request $request)
    {
        // The form has always asked for a phone number and an interest, and
        // neither was validated — so both were dropped on the way to the
        // database. Someone asking for a callback lost their number.
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email:rfc', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'interest' => ['nullable', 'string', 'in:buying,selling,renting,letting,other'],
            // whereNull('deleted_at'), because plain `exists` matches a
            // soft-deleted row: a question about a withdrawn listing was filed
            // against it silently and the message below could never fire.
            'property_id' => ['nullable', 'integer', Rule::exists('properties', 'id')->whereNull('deleted_at')],
            'message' => ['required', 'string', 'max:5000'],
        ], [
            // Errors name the fix, in the interface's voice. No apology, no
            // "invalid", no telling someone their input was bad.
            'name.required' => __('Add your name so we know who is asking.'),
            'email.required' => __('Add an email address so we can reply.'),
            'email.email' => __('Add the part after the @ so we can reply.'),
            'message.required' => __('Tell us what you would like to know.'),
            'message.max' => __('Keep it under 5,000 characters and we will pick up the rest by phone.'),
            'property_id.exists' => __('That property is no longer listed. Send the question without it and we will find it.'),
        ]);

        $enquiry = ContactMessage::create($validated);

        // The form promises a reply within one working day. Nothing read this
        // table — no resource, no notification — so the promise had nothing
        // behind it. The row is already saved, so a mail failure is logged
        // rather than shown to someone who did nothing wrong.
        try {
            Mail::to(app(GeneralSettings::class)->site_email)
                ->send(new EnquiryReceived($enquiry->load('property')));
        } catch (\Throwable $e) {
            Log::error('Could not send the enquiry notification', [
                'enquiry' => $enquiry->id,
                'exception' => $e->getMessage(),
            ]);
        }

        // Carrying the property through: without it the confirmation page
        // loses the "Asking about ..." panel and its hidden property_id, so a
        // second question from that page is filed against nothing.
        return redirect()
            ->route('contact.show', array_filter(['property' => $enquiry->property_id]))
            ->with('success', __('Message sent. We reply within one working day.'));
    }
}
