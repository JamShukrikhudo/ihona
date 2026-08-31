<?php

declare(strict_types=1);

namespace Liberu\RealEstate\PropertiesLivewire\Components;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Liberu\RealEstate\Properties\Application\CreatePriceAlert;
use Liberu\RealEstate\Properties\Application\DeletePriceAlert;
use Liberu\RealEstate\Properties\Application\TogglePriceAlert;
use Liberu\RealEstate\Properties\Models\Property;
use Liberu\RealEstate\Properties\Models\PropertyPriceAlert;
use Livewire\Attributes\Validate;
use Livewire\Component;

final class PriceAlertManager extends Component
{
    public int|string $propertyId;

    #[Validate('required|numeric|min:0.1|max:100')]
    public float $alertPercentage = 5;

    #[Validate('required|in:daily,weekly,monthly')]
    public string $alertFrequency = 'daily';

    /** @var list<array<string, mixed>> */
    public array $priceAlerts = [];

    public function mount(int|string $propertyId): void
    {
        $this->propertyId = $propertyId;
        $this->loadPriceAlerts();
    }

    public function createAlert(CreatePriceAlert $create): void
    {
        $this->validate();
        $user = Auth::user();
        $teamId = $user?->current_team_id;

        if (! $user || ! $teamId) {
            throw ValidationException::withMessages(['alert' => 'Select a team before creating a price alert.']);
        }

        $create->handle($teamId, $user->getAuthIdentifier(), $this->propertyId, [
            'alert_percentage' => $this->alertPercentage,
            'alert_frequency' => $this->alertFrequency,
        ]);

        $this->loadPriceAlerts();
        $this->reset(['alertPercentage', 'alertFrequency']);
        $this->alertPercentage = 5;
        $this->alertFrequency = 'daily';
        session()->flash('message', 'Price alert created successfully.');
    }

    public function toggleAlert(int|string $alertId, TogglePriceAlert $toggle): void
    {
        $toggle->handle($this->teamId(), $this->userId(), $alertId);
        $this->loadPriceAlerts();
    }

    public function deleteAlert(int|string $alertId, DeletePriceAlert $delete): void
    {
        $delete->handle($this->teamId(), $this->userId(), $alertId);
        $this->loadPriceAlerts();
        session()->flash('message', 'Price alert deleted successfully.');
    }

    public function render(): View
    {
        return view('real-estate-properties-livewire::price-alert-manager', ['property' => $this->property()]);
    }

    private function loadPriceAlerts(): void
    {
        $this->priceAlerts = PropertyPriceAlert::query()
            ->forUser($this->teamId(), $this->userId())
            ->where('property_id', $this->propertyId)
            ->latest()
            ->get()
            ->map(fn (PropertyPriceAlert $alert): array => $alert->only(['id', 'initial_price', 'alert_percentage', 'alert_frequency', 'is_active']))
            ->all();
    }

    private function property(): Property
    {
        return Property::query()->forTeam($this->teamId())->whereKey($this->propertyId)->firstOrFail();
    }

    private function teamId(): int|string
    {
        $teamId = Auth::user()?->current_team_id;
        abort_unless($teamId, 403);

        return $teamId;
    }

    private function userId(): int|string
    {
        $userId = Auth::id();
        abort_unless($userId, 403);

        return $userId;
    }
}
