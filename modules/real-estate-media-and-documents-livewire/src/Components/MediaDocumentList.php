<?php

declare(strict_types=1);

namespace Liberu\RealEstate\MediaAndDocumentsLivewire\Components;

use Illuminate\Contracts\View\View;
use Liberu\RealEstate\MediaAndDocuments\Models\MediaDocument;
use Livewire\Attributes\Validate;
use Livewire\Component;

final class MediaDocumentList extends Component
{
    #[Validate('nullable|string|max:255')]
    public string $search = '';

    public function render(): View
    {
        $teamId = auth()->user()?->current_team_id;
        $documents = $teamId === null ? collect() : MediaDocument::query()->forTeam($teamId)->when($this->search !== '', fn ($query) => $query->where(function ($query) {
            $query->where('title', 'like', '%'.$this->search.'%')->orWhere('path', 'like', '%'.$this->search.'%');
        }))->latest()->paginate(20);

        return view('real-estate-media-and-documents-livewire::media-document-list', ['documents' => $documents]);
    }
}
