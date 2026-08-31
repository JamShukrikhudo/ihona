<?php

declare(strict_types=1);

namespace Liberu\RealEstate\PartiesApi\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Liberu\RealEstate\Parties\Application\CreateContact;
use Liberu\RealEstate\Parties\Models\Contact;
use Liberu\RealEstate\PartiesApi\Http\Resources\ContactResource;

final class ContactController
{
    public function store(Request $request, CreateContact $create): JsonResponse
    {
        $user = $request->user();
        abort_unless($user?->current_team_id !== null, 403);
        $data = $request->validate([
            'branch_id' => ['nullable', 'integer'], 'company_id' => ['nullable', 'integer'],
            'type' => ['sometimes', 'string', 'in:applicant,buyer,vendor,landlord,tenant,solicitor,contractor'],
            'title' => ['nullable', 'string', 'max:30'], 'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['nullable', 'string', 'max:255'], 'emails' => ['nullable', 'array'],
            'phones' => ['nullable', 'array'], 'addresses' => ['nullable', 'array'], 'tags' => ['nullable', 'array'],
            'notes' => ['nullable', 'string', 'max:5000'], 'preferred_language' => ['nullable', 'string', 'max:10'],
            'status' => ['sometimes', 'string', 'in:active,inactive'],
        ]);

        return (new ContactResource($create->handle($user->current_team_id, $user->getAuthIdentifier(), $data)))->response()->setStatusCode(201);
    }

    public function update(Request $request, Contact $contact): JsonResponse
    {
        abort_unless((string) $request->user()?->current_team_id === (string) $contact->team_id, 404);
        $data = $request->validate(['first_name' => ['sometimes', 'string', 'max:255'], 'last_name' => ['nullable', 'string', 'max:255'], 'status' => ['sometimes', 'string', 'in:active,inactive'], 'emails' => ['nullable', 'array'], 'phones' => ['nullable', 'array'], 'notes' => ['nullable', 'string', 'max:5000']]);
        $contact->fill($data)->save();

        return (new ContactResource($contact->refresh()))->response();
    }

    public function show(Request $request, Contact $contact): JsonResponse
    {
        abort_unless((string) $request->user()?->current_team_id === (string) $contact->team_id, 404);

        return (new ContactResource($contact))->response();
    }

    public function destroy(Request $request, Contact $contact): Response
    {
        abort_unless((string) $request->user()?->current_team_id === (string) $contact->team_id, 404);
        $contact->delete();

        return response()->noContent();
    }
}
