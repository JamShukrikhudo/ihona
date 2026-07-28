<?php

namespace App\Services;

use App\Models\AgencyTask;
use App\Models\Booking;
use App\Models\Company;
use App\Models\Contact;
use App\Models\Document;
use App\Models\MaintenanceRequest;
use App\Models\Offer;
use App\Models\Property;
use App\Models\Team;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class GlobalSearchService
{
    public const TYPES = [
        'properties', 'contacts', 'companies', 'documents', 'viewings',
        'tasks', 'offers', 'maintenance', 'staff',
    ];

    public function search(Team $team, string $term, array $types = [], int $limit = 10): array
    {
        $types = $types === [] ? self::TYPES : array_values(array_intersect(self::TYPES, $types));
        $like = '%'.$term.'%';
        $results = [];

        foreach ($types as $type) {
            $results[$type] = $this->{$type}($team, $like, $limit)->values();
        }

        return $results;
    }

    private function properties(Team $team, string $like, int $limit): Collection
    {
        return Property::query()->where('team_id', $team->id)
            ->where(fn (Builder $query) => $query->where('title', 'like', $like)
                ->orWhere('location', 'like', $like)->orWhere('postal_code', 'like', $like))
            ->limit($limit)->get()
            ->map(fn (Property $record) => $this->result('property', $record->id, $record->title, $record->location, [
                'status' => $record->status, 'price' => $record->price,
            ]));
    }

    private function contacts(Team $team, string $like, int $limit): Collection
    {
        return Contact::query()->where('team_id', $team->id)
            ->where(fn (Builder $query) => $query->where('first_name', 'like', $like)
                ->orWhere('last_name', 'like', $like)->orWhere('notes', 'like', $like))
            ->limit($limit)->get()
            ->map(fn (Contact $record) => $this->result(
                'contact',
                $record->id,
                trim("$record->first_name $record->last_name"),
                $record->type,
                ['status' => $record->status],
            ));
    }

    private function companies(Team $team, string $like, int $limit): Collection
    {
        return Company::query()->where('team_id', $team->id)
            ->where(fn (Builder $query) => $query->where('name', 'like', $like)
                ->orWhere('registration_number', 'like', $like)->orWhere('email', 'like', $like))
            ->limit($limit)->get()
            ->map(fn (Company $record) => $this->result('company', $record->id, $record->name, $record->type));
    }

    private function documents(Team $team, string $like, int $limit): Collection
    {
        return Document::query()->where('team_id', $team->id)
            ->where(fn (Builder $query) => $query->where('title', 'like', $like)
                ->orWhere('description', 'like', $like))
            ->limit($limit)->get()
            ->map(fn (Document $record) => $this->result('document', $record->id, $record->title, $record->file_type));
    }

    private function viewings(Team $team, string $like, int $limit): Collection
    {
        return Booking::query()->where('team_id', $team->id)
            ->where(fn (Builder $query) => $query->where('name', 'like', $like)
                ->orWhere('contact', 'like', $like)->orWhere('notes', 'like', $like))
            ->limit($limit)->get()
            ->map(fn (Booking $record) => $this->result('viewing', $record->id, $record->name ?: 'Viewing', $record->contact, [
                'date' => $record->date?->toDateString(), 'status' => $record->status,
            ]));
    }

    private function tasks(Team $team, string $like, int $limit): Collection
    {
        return AgencyTask::query()->where('team_id', $team->id)
            ->where(fn (Builder $query) => $query->where('title', 'like', $like)
                ->orWhere('description', 'like', $like))
            ->limit($limit)->get()
            ->map(fn (AgencyTask $record) => $this->result('task', $record->id, $record->title, $record->description, [
                'status' => $record->status, 'due_at' => $record->due_at?->toIso8601String(),
            ]));
    }

    private function offers(Team $team, string $like, int $limit): Collection
    {
        return Offer::query()->where('team_id', $team->id)
            ->with(['property:id,title', 'contact:id,first_name,last_name'])
            ->where(fn (Builder $query) => $query->where('conditions', 'like', $like)
                ->orWhere('chain_information', 'like', $like)->orWhere('status', 'like', $like))
            ->limit($limit)->get()
            ->map(fn (Offer $record) => $this->result(
                'offer',
                $record->id,
                $record->property?->title ?? 'Offer',
                trim(($record->contact?->first_name ?? '').' '.($record->contact?->last_name ?? '')),
                ['status' => $record->status, 'amount' => $record->amount, 'currency' => $record->currency],
            ));
    }

    private function maintenance(Team $team, string $like, int $limit): Collection
    {
        return MaintenanceRequest::query()
            ->whereHas('property', fn (Builder $query) => $query->where('team_id', $team->id))
            ->where(fn (Builder $query) => $query->where('title', 'like', $like)
                ->orWhere('description', 'like', $like))
            ->limit($limit)->get()
            ->map(fn (MaintenanceRequest $record) => $this->result(
                'maintenance',
                $record->id,
                $record->title,
                $record->description,
                ['status' => $record->status],
            ));
    }

    private function staff(Team $team, string $like, int $limit): Collection
    {
        return $team->users()->where(fn (Builder $query) => $query
            ->where('name', 'like', $like)->orWhere('email', 'like', $like))
            ->limit($limit)->get()
            ->map(fn ($record) => $this->result('staff', $record->id, $record->name, $record->email));
    }

    private function result(string $type, int $id, string $title, ?string $subtitle, array $meta = []): array
    {
        return compact('type', 'id', 'title', 'subtitle', 'meta');
    }
}
