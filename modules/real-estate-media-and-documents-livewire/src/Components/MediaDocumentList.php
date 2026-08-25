<?php

declare(strict_types=1);

namespace Liberu\RealEstate\MediaAndDocumentsLivewire\Components;

use Illuminate\Contracts\View\View;
use Liberu\RealEstate\MediaAndDocuments\Application\GeneratePropertyBrochure;
use Liberu\RealEstate\MediaAndDocuments\Application\ReorderMediaDocument;
use Liberu\RealEstate\MediaAndDocuments\Application\SetMediaDocumentRetention;
use Liberu\RealEstate\MediaAndDocuments\Application\UpdateMediaRights;
use Liberu\RealEstate\MediaAndDocuments\Models\MediaDocument;
use Livewire\Attributes\Validate;
use Livewire\Component;

final class MediaDocumentList extends Component
{
    #[Validate('nullable|string|max:255')]
    public string $search = '';

    /** @param array<string, mixed> $property @param array<string, mixed> $options */
    public function generateBrochure(array $property, array $options, GeneratePropertyBrochure $generate): array
    {
        return $generate->handle($property, $options);
    }

    /** @param array<string, mixed> $rights */
    public function updateRights(int $documentId, array $rights): void
    {
        $teamId = (int) auth()->user()->current_team_id;
        $document = MediaDocument::query()->forTeam($teamId)->findOrFail($documentId);
        app(UpdateMediaRights::class)->handle($document, $teamId, $rights);
    }

    public function reorder(int $documentId, int $sortOrder): void
    {
        $teamId = (int) auth()->user()->current_team_id;
        $document = MediaDocument::query()->forTeam($teamId)->findOrFail($documentId);
        app(ReorderMediaDocument::class)->handle($document, $teamId, $sortOrder);
    }

    public function setRetention(int $documentId, ?string $retentionUntil): void
    {
        $teamId = (int) auth()->user()->current_team_id;
        $document = MediaDocument::query()->forTeam($teamId)->findOrFail($documentId);
        app(SetMediaDocumentRetention::class)->handle($document, $teamId, $retentionUntil);
    }

    public function render(): View
    {
        $teamId = auth()->user()?->current_team_id;
        $documents = $teamId === null ? collect() : MediaDocument::query()->forTeam($teamId)->when($this->search !== '', fn ($query) => $query->where(function ($query) {
            $query->where('title', 'like', '%'.$this->search.'%')->orWhere('path', 'like', '%'.$this->search.'%');
        }))->latest()->paginate(20);

        return view('real-estate-media-and-documents-livewire::media-document-list', ['documents' => $documents]);
    }
}
