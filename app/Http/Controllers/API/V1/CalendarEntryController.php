<?php

namespace App\Http\Controllers\API\V1;

use App\Models\CalendarEntry;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class CalendarEntryController extends TenantCrudController
{
    protected string $model = CalendarEntry::class;

    protected string $routeParameter = 'calendar_entry';

    protected array $searchable = ['title', 'description', 'location'];

    protected array $filterable = ['type', 'status', 'branch_id', 'property_id', 'organiser_id'];

    protected function rules(Request $request, ?Model $record = null): array
    {
        $teamId = $this->teamId($request);

        return [
            'type' => [$record ? 'sometimes' : 'required', Rule::in(['meeting', 'reminder'])],
            'title' => [$record ? 'sometimes' : 'required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'location' => ['nullable', 'string', 'max:255'],
            'branch_id' => ['nullable', Rule::exists('branches', 'id')->where('team_id', $teamId)],
            'property_id' => ['nullable', Rule::exists('properties', 'id')->where('team_id', $teamId)],
            'organiser_id' => ['nullable', Rule::exists('team_user', 'user_id')->where('team_id', $teamId)],
            'starts_at' => [$record ? 'sometimes' : 'required', 'date'],
            'ends_at' => ['nullable', 'date'],
            'reminder_at' => ['nullable', 'date'],
            'all_day' => ['sometimes', 'boolean'],
            'status' => ['sometimes', Rule::in(['scheduled', 'completed', 'cancelled'])],
            'attendee_user_ids' => ['nullable', 'array', 'max:100'],
            'attendee_user_ids.*' => ['integer', 'distinct', Rule::exists('team_user', 'user_id')->where('team_id', $teamId)],
            'contact_ids' => ['nullable', 'array', 'max:100'],
            'contact_ids.*' => ['integer', 'distinct', Rule::exists('contacts', 'id')->where('team_id', $teamId)],
            'recurrence' => ['nullable', 'array'],
            'recurrence.frequency' => ['required_with:recurrence', Rule::in(['daily', 'weekly', 'monthly', 'yearly'])],
            'recurrence.interval' => ['sometimes', 'integer', 'between:1,365'],
            'recurrence.until' => ['nullable', 'date', 'after_or_equal:starts_at'],
            'recurrence.count' => ['nullable', 'integer', 'between:1,1000'],
            'recurrence.weekdays' => ['nullable', 'array', 'max:7'],
            'recurrence.weekdays.*' => ['integer', 'distinct', 'between:1,7'],
        ];
    }

    protected function prepareForCreate(Request $request, array $attributes): array
    {
        $attributes['created_by'] = $request->user()->id;
        $attributes['organiser_id'] ??= $request->user()->id;
        $this->validateDates($attributes);

        return $attributes;
    }

    protected function prepareForUpdate(Request $request, Model $record, array $attributes): array
    {
        $this->validateDates(array_merge($record->only(['starts_at', 'ends_at', 'reminder_at']), $attributes));

        return $attributes;
    }

    private function validateDates(array $attributes): void
    {
        $startsAt = Carbon::parse($attributes['starts_at']);

        if (filled($attributes['ends_at'] ?? null) && Carbon::parse($attributes['ends_at'])->lt($startsAt)) {
            throw ValidationException::withMessages([
                'ends_at' => ['The end time must be after or equal to the start time.'],
            ]);
        }
        if (filled($attributes['reminder_at'] ?? null) && Carbon::parse($attributes['reminder_at'])->gt($startsAt)) {
            throw ValidationException::withMessages([
                'reminder_at' => ['The reminder time must be before or equal to the start time.'],
            ]);
        }
    }
}
