<?php

declare(strict_types=1);

namespace Liberu\RealEstate\PropertiesLivewire\Components;

use Carbon\CarbonImmutable;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Schema;
use Liberu\RealEstate\MediaAndDocuments\Models\MediaDocument;
use Liberu\RealEstate\Properties\Application\TogglePropertyFavorite;
use Liberu\RealEstate\Properties\Models\Property;
use Liberu\RealEstate\Viewings\Application\CreateViewing;
use Livewire\Component;

final class PropertyDetail extends Component
{
    public int|string $propertyId;

    public bool $isFavorited = false;

    public bool $showVirtualTour = false;

    public bool $show3dModel = false;

    public bool $showScheduleLiveTourModal = false;

    public ?string $tourDate = null;

    public ?string $tourTime = null;

    public ?string $tourNotes = null;

    public function mount(int|string $propertyId): void
    {
        $this->propertyId = $propertyId;
        $this->isFavorited = $this->property()->favorites()
            ->where('user_id', auth()->id())
            ->exists();
    }

    public function toggleFavorite(TogglePropertyFavorite $toggle): void
    {
        $user = auth()->user();
        abort_unless($user?->current_team_id !== null, 403);

        $this->isFavorited = $toggle->handle(
            $user->current_team_id,
            $user->getAuthIdentifier(),
            $this->property()->getKey(),
        );
    }

    public function requestViewing(): void
    {
        $this->dispatch('property-viewing-requested', propertyId: $this->property()->getKey());
    }

    public function openScheduleLiveTourModal()
    {
        if (auth()->guest()) {
            return redirect('/login');
        }

        abort_unless((bool) $this->property()->live_tour_available, 404);
        $this->showScheduleLiveTourModal = true;
    }

    public function closeScheduleLiveTourModal(): void
    {
        $this->showScheduleLiveTourModal = false;
        $this->reset(['tourDate', 'tourTime', 'tourNotes']);
    }

    public function scheduleLiveTour(CreateViewing $create): void
    {
        $this->validate([
            'tourDate' => ['required', 'date', 'after:today'],
            'tourTime' => ['required', 'date_format:H:i'],
            'tourNotes' => ['nullable', 'string', 'max:500'],
        ]);

        $user = auth()->user();
        abort_unless($user?->current_team_id !== null, 403);
        $property = $this->property();
        abort_unless((bool) $property->live_tour_available, 404);
        $startsAt = CarbonImmutable::parse($this->tourDate.' '.$this->tourTime);

        $create->handle($user->current_team_id, $user->getAuthIdentifier(), [
            'property_id' => $property->getKey(),
            'subject' => 'Live virtual tour',
            'starts_at' => $startsAt,
            'ends_at' => $startsAt->addHour(),
            'access' => ['mode' => 'virtual'],
            'accompaniment' => ['notes' => $this->tourNotes],
        ]);

        $this->closeScheduleLiveTourModal();
        $this->dispatch('tourScheduled');
    }

    public function toggleVirtualTour(): void
    {
        $this->showVirtualTour = ! $this->showVirtualTour;
    }

    public function toggle3dModel(): void
    {
        $this->show3dModel = ! $this->show3dModel;
    }

    public function render(): View
    {
        $property = $this->property();

        return view('real-estate-properties-livewire::property-detail', [
            'property' => $property,
            'facts' => $property->disclosureFacts(),
            'gallery' => $property->galleryItems($this->mediaItems($property)),
            'videoUrl' => $this->videoUrl($property),
        ]);
    }

    /** @return array<int, array{url?: string|null, kind?: string|null, caption?: string|null, staged?: bool}> */
    private function mediaItems(Property $property): array
    {
        if (! Schema::hasTable('real_estate_media_documents')) {
            return [];
        }

        return MediaDocument::query()
            ->forTeam($property->team_id)
            ->where('property_id', $property->getKey())
            ->whereIn('kind', ['photo', 'floorplan', 'siteplan'])
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get()
            ->map(fn (MediaDocument $document): array => [
                'url' => $document->publicUrl(),
                'kind' => $document->galleryKind(),
                'caption' => $document->title,
                'staged' => (bool) data_get($document->metadata, 'staged', false),
            ])->all();
    }

    private function videoUrl(Property $property): ?string
    {
        if (! Schema::hasTable('real_estate_media_documents')) {
            return null;
        }

        $video = MediaDocument::query()
            ->forTeam($property->team_id)
            ->where('property_id', $property->getKey())
            ->where('kind', 'video')
            ->orderBy('sort_order')
            ->orderBy('id')
            ->first();

        return $video?->isVideo() ? $video->publicUrl() : null;
    }

    private function property(): Property
    {
        $teamId = auth()->user()?->current_team_id;
        abort_unless($teamId !== null, 403);

        return Property::query()->forTeam($teamId)->findOrFail($this->propertyId);
    }
}
