<?php

namespace App\Http\Controllers;

use App\Models\Property;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

/**
 * Applying to rent is not built.
 *
 * What was here rendered a view that does not exist (a 500 on a page linked
 * twice from the property detail page), and its store() created a user account
 * with a password, assigned the tenant role, stored no application at all —
 * the comment in place of that was "Here you would typically create a tenancy
 * application record" — and redirected to a route name that does not exist.
 * index() queried a tenancy_applications table that has never been migrated.
 *
 * Until the real form exists (ticket 19), an applicant is sent somewhere that
 * works and reaches a person, rather than an error page.
 */
class TenancyApplicationController extends Controller
{
    public function create(Property $property): RedirectResponse
    {
        // Built rather than taken from redirect(): once Livewire has rendered a
        // component in the same process it swaps that binding for its own
        // Redirector, which a plain controller cannot return — the response
        // then fails as it is cast to content. Only surfaced by walking the
        // whole site in one process.
        return new RedirectResponse(route('contact.show', [
            'property' => $property->id,
            'interest' => 'renting',
        ]));
    }

    public function store(Request $request, Property $property): RedirectResponse
    {
        // A stale or bookmarked form can still post here. Saying nothing would
        // leave someone believing they had applied.
        return $this->create($property)->with(
            'error',
            __('Applications are not open through that form yet. Send this instead and we will take it from there.')
        );
    }
}
