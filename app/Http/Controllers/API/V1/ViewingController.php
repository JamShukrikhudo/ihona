<?php

namespace App\Http\Controllers\API\V1;

use App\Models\Appointment;
use App\Models\ViewingFeedback;
use App\Services\ViewingFeedbackService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;

class ViewingController extends TenantCrudController
{
    protected string $model = Appointment::class;

    protected string $routeParameter = 'viewing';

    protected array $searchable = ['name', 'contact', 'notes'];

    protected array $filterable = ['property_id', 'agent_id', 'staff_id', 'status', 'outcome'];

    protected function rules(Request $request, ?Model $record = null): array
    {
        $teamId = $this->teamId($request);

        return [
            'property_id' => [$record ? 'sometimes' : 'required', Rule::exists('properties', 'id')->where('team_id', $teamId)],
            'agent_id' => ['nullable', Rule::exists('team_user', 'user_id')->where('team_id', $teamId)],
            'staff_id' => ['nullable', Rule::exists('team_user', 'user_id')->where('team_id', $teamId)],
            'appointment_date' => [$record ? 'sometimes' : 'required', 'date'],
            'status' => ['sometimes', Rule::in(['scheduled', 'confirmed', 'completed', 'cancelled', 'no_show'])],
            'name' => ['nullable', 'string', 'max:255'],
            'contact' => ['nullable', 'string', 'max:255'],
            'attendees' => ['nullable', 'array', 'max:25'],
            'attendees.*.name' => ['required_with:attendees', 'string', 'max:255'],
            'attendees.*.email' => ['nullable', 'email:rfc', 'max:255'],
            'attendees.*.phone' => ['nullable', 'string', 'max:50'],
            'attendees.*.contact_id' => ['nullable', Rule::exists('contacts', 'id')->where('team_id', $teamId)],
            'notes' => ['nullable', 'string'],
            'outcome' => ['nullable', Rule::in(['interested', 'offer_expected', 'follow_up', 'not_interested', 'no_show'])],
            'outcome_notes' => ['nullable', 'string'],
        ];
    }

    protected function prepareForCreate(Request $request, array $attributes): array
    {
        $attributes['user_id'] = $request->user()->id;

        return $attributes;
    }

    public function sendConfirmation(Request $request): JsonResponse
    {
        $viewing = $this->viewing($request);
        $recipients = $this->recipients($viewing);

        if ($recipients === []) {
            return response()->json([
                'message' => 'At least one attendee email address is required.',
            ], 422);
        }

        $this->sendScheduleEmail($viewing, $recipients, 'Viewing confirmation');
        $viewing->forceFill(['confirmation_sent_at' => now()])->save();

        return response()->json(['data' => $viewing->fresh()]);
    }

    public function sendReminder(Request $request): JsonResponse
    {
        $viewing = $this->viewing($request);

        if ($viewing->appointment_date->isPast() || $viewing->status === 'cancelled') {
            return response()->json([
                'message' => 'Reminders can only be sent for an upcoming, active viewing.',
            ], 422);
        }

        $recipients = $this->recipients($viewing);
        if ($recipients === []) {
            return response()->json([
                'message' => 'At least one attendee email address is required.',
            ], 422);
        }

        $this->sendScheduleEmail($viewing, $recipients, 'Viewing reminder');
        $viewing->forceFill(['reminder_sent_at' => now()])->save();

        return response()->json(['data' => $viewing->fresh()]);
    }

    public function feedback(Request $request): JsonResponse
    {
        $viewing = $this->viewing($request);

        return response()->json([
            'data' => ViewingFeedback::query()
                ->where('team_id', $this->teamId($request))
                ->where('appointment_id', $viewing->getKey())
                ->latest()
                ->get(),
        ]);
    }

    public function requestFeedback(
        Request $request,
        ViewingFeedbackService $service
    ): JsonResponse {
        $viewing = $this->viewing($request);
        $attributes = $request->validate([
            'attendees' => ['required', 'array', 'min:1', 'max:25'],
            'attendees.*.name' => ['required', 'string', 'max:255'],
            'attendees.*.email' => ['required', 'email:rfc', 'max:255'],
        ]);

        $created = collect($attributes['attendees'])->map(
            fn (array $attendee) => $service->requestFeedback(
                $viewing,
                $attendee['email'],
                $attendee['name'],
            )
        );

        return response()->json(['data' => $created], 201);
    }

    public function submitFeedback(
        Request $request,
        ViewingFeedbackService $service
    ): JsonResponse {
        $viewing = $this->viewing($request);
        $feedback = ViewingFeedback::query()
            ->where('team_id', $this->teamId($request))
            ->where('appointment_id', $viewing->getKey())
            ->findOrFail($request->route('feedback'));

        $attributes = $request->validate([
            'overall_rating' => ['nullable', 'integer', 'between:1,5'],
            'price_rating' => ['nullable', 'integer', 'between:1,5'],
            'condition_rating' => ['nullable', 'integer', 'between:1,5'],
            'location_rating' => ['nullable', 'integer', 'between:1,5'],
            'size_rating' => ['nullable', 'integer', 'between:1,5'],
            'positive_comments' => ['nullable', 'string', 'max:5000'],
            'negative_comments' => ['nullable', 'string', 'max:5000'],
            'general_comments' => ['nullable', 'string', 'max:5000'],
            'interest_level' => ['nullable', Rule::in([
                'very_interested', 'interested', 'neutral', 'not_interested', 'definitely_not',
            ])],
            'would_make_offer' => ['sometimes', 'boolean'],
            'offer_price' => ['nullable', 'numeric', 'min:0', 'required_if:would_make_offer,true'],
        ]);

        return response()->json(['data' => $service->submitFeedback($feedback, $attributes)]);
    }

    private function viewing(Request $request): Appointment
    {
        /** @var Appointment $viewing */
        $viewing = $this->record($request);

        return $viewing;
    }

    private function recipients(Appointment $viewing): array
    {
        return collect($viewing->attendees ?? [])
            ->pluck('email')
            ->filter(fn ($email) => is_string($email) && filter_var($email, FILTER_VALIDATE_EMAIL))
            ->unique()
            ->values()
            ->all();
    }

    private function sendScheduleEmail(Appointment $viewing, array $recipients, string $subject): void
    {
        $date = $viewing->appointment_date->toDayDateTimeString();
        $property = e($viewing->property?->title ?? 'the property');

        Mail::html(
            "<p>{$subject} for <strong>{$property}</strong> on {$date}.</p>",
            fn ($message) => $message
                ->from(config('mail.from.address') ?: 'noreply@example.test')
                ->to($recipients)
                ->subject($subject)
        );
    }
}
