<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use App\Models\ContactRelationship;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class ContactRelationshipController extends Controller
{
    public function index(Request $request, Contact $contact): JsonResponse
    {
        $contact = $this->contact($request, $contact);

        $relationships = ContactRelationship::query()
            ->where('team_id', $this->teamId($request))
            ->where(function ($query) use ($contact): void {
                $query->where('contact_id', $contact->id)
                    ->orWhere('related_contact_id', $contact->id);
            })
            ->with([
                'contact:id,first_name,last_name,type',
                'relatedContact:id,first_name,last_name,type',
                'creator:id,name,email',
            ])
            ->latest()
            ->get()
            ->map(fn (ContactRelationship $relationship) => $this->forContact($relationship, $contact));

        return response()->json(['data' => $relationships]);
    }

    public function store(Request $request, Contact $contact): JsonResponse
    {
        $contact = $this->contact($request, $contact);
        $teamId = $this->teamId($request);
        $attributes = $request->validate([
            'related_contact_id' => [
                'required',
                'integer',
                Rule::notIn([$contact->id]),
                Rule::exists('contacts', 'id')->where('team_id', $teamId),
            ],
            'relationship' => ['required', 'string', 'max:100'],
            'inverse_relationship' => ['required', 'string', 'max:100'],
            'notes' => ['nullable', 'string', 'max:5000'],
        ]);

        $duplicate = ContactRelationship::query()
            ->where('team_id', $teamId)
            ->where(function ($query) use ($contact, $attributes): void {
                $query->where(function ($query) use ($contact, $attributes): void {
                    $query->where('contact_id', $contact->id)
                        ->where('related_contact_id', $attributes['related_contact_id']);
                })->orWhere(function ($query) use ($contact, $attributes): void {
                    $query->where('contact_id', $attributes['related_contact_id'])
                        ->where('related_contact_id', $contact->id);
                });
            })
            ->exists();

        if ($duplicate) {
            throw ValidationException::withMessages([
                'related_contact_id' => ['These contacts already have a relationship record.'],
            ]);
        }

        $relationship = ContactRelationship::create([
            ...$attributes,
            'team_id' => $teamId,
            'contact_id' => $contact->id,
            'created_by' => $request->user()->id,
        ])->load(['contact', 'relatedContact', 'creator']);

        return response()->json([
            'data' => $this->forContact($relationship, $contact),
        ], 201);
    }

    public function update(
        Request $request,
        Contact $contact,
        ContactRelationship $relationship
    ): JsonResponse {
        $contact = $this->contact($request, $contact);
        $relationship = $this->relationship($request, $contact, $relationship);
        $attributes = $request->validate([
            'relationship' => ['sometimes', 'required', 'string', 'max:100'],
            'inverse_relationship' => ['sometimes', 'required', 'string', 'max:100'],
            'notes' => ['nullable', 'string', 'max:5000'],
        ]);

        if ($relationship->contact_id === $contact->id) {
            $relationship->update($attributes);
        } else {
            $relationship->update([
                ...$attributes,
                'relationship' => $attributes['inverse_relationship'] ?? $relationship->relationship,
                'inverse_relationship' => $attributes['relationship'] ?? $relationship->inverse_relationship,
            ]);
        }

        return response()->json([
            'data' => $this->forContact(
                $relationship->fresh()->load(['contact', 'relatedContact', 'creator']),
                $contact
            ),
        ]);
    }

    public function destroy(
        Request $request,
        Contact $contact,
        ContactRelationship $relationship
    ): JsonResponse {
        $contact = $this->contact($request, $contact);
        $this->relationship($request, $contact, $relationship)->delete();

        return response()->json(null, 204);
    }

    private function forContact(ContactRelationship $relationship, Contact $contact): array
    {
        $forward = $relationship->contact_id === $contact->id;

        return [
            'id' => $relationship->id,
            'contact_id' => $contact->id,
            'related_contact' => $forward
                ? $relationship->relatedContact
                : $relationship->contact,
            'relationship' => $forward
                ? $relationship->relationship
                : $relationship->inverse_relationship,
            'inverse_relationship' => $forward
                ? $relationship->inverse_relationship
                : $relationship->relationship,
            'notes' => $relationship->notes,
            'created_by' => $relationship->creator,
            'created_at' => $relationship->created_at,
            'updated_at' => $relationship->updated_at,
        ];
    }

    private function contact(Request $request, Contact $contact): Contact
    {
        abort_unless($contact->team_id === $this->teamId($request), 404);

        return $contact;
    }

    private function relationship(
        Request $request,
        Contact $contact,
        ContactRelationship $relationship
    ): ContactRelationship {
        abort_unless(
            $relationship->team_id === $this->teamId($request)
            && in_array($contact->id, [
                $relationship->contact_id,
                $relationship->related_contact_id,
            ], true),
            404
        );

        return $relationship;
    }

    private function teamId(Request $request): int
    {
        $teamId = $request->user()?->current_team_id;

        if (! $teamId || ! $request->user()->allTeams()->contains('id', $teamId)) {
            throw ValidationException::withMessages([
                'team' => ['Select an organisation you belong to before using this endpoint.'],
            ]);
        }

        return (int) $teamId;
    }
}
