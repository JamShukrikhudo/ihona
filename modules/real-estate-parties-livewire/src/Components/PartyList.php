<?php

declare(strict_types=1);

namespace Liberu\RealEstate\PartiesLivewire\Components;

use Illuminate\Contracts\View\View;
use Liberu\RealEstate\Parties\Application\CreatePartyRelationship;
use Liberu\RealEstate\Parties\Application\DeletePartyRelationship;
use Liberu\RealEstate\Parties\Application\ManagePartyConsent;
use Liberu\RealEstate\Parties\Models\Party;
use Liberu\RealEstate\Parties\Models\PartyRelationship;
use Livewire\Attributes\Validate;
use Livewire\Component;

final class PartyList extends Component
{
    #[Validate('nullable|string|max:255')]
    public string $search = '';

    public function setConsent(int $partyId, bool $granted): void
    {
        $teamId = (int) auth()->user()->current_team_id;
        $party = Party::query()->forTeam($teamId)->findOrFail($partyId);
        app(ManagePartyConsent::class)->handle($party, $teamId, $granted);
    }

    /** @param array<string, mixed> $attributes */
    public function addRelationship(int $partyId, array $attributes, CreatePartyRelationship $create): void
    {
        $teamId = (int) auth()->user()->current_team_id;
        $party = Party::query()->forTeam($teamId)->findOrFail($partyId);
        $create->handle($party, $teamId, $attributes);
    }

    public function removeRelationship(int $relationshipId, DeletePartyRelationship $delete): void
    {
        $teamId = (int) auth()->user()->current_team_id;
        $relationship = PartyRelationship::query()->forTeam($teamId)->findOrFail($relationshipId);
        $delete->handle($relationship, $teamId);
    }

    public function render(): View
    {
        $teamId = auth()->user()?->current_team_id;
        $parties = $teamId === null
            ? collect()
            : Party::query()->forTeam($teamId)->when($this->search !== '', fn ($query) => $query->where('name', 'like', '%'.$this->search.'%'))->latest()->paginate(20);

        return view('real-estate-parties-livewire::party-list', ['parties' => $parties]);
    }
}
