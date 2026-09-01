<?php

declare(strict_types=1);

namespace Liberu\RealEstate\PropertyManagementLivewire\Components;

use Illuminate\Contracts\View\View;
use Liberu\RealEstate\PropertyManagement\Application\CreateMaintenanceRequest;
use Livewire\Component;

final class MaintenanceRequestForm extends Component
{
    public string $title = '';

    public string $description = '';

    public int|string|null $property_id = null;

    public function submit(CreateMaintenanceRequest $create): void
    {
        $data = $this->validate(['title' => ['required', 'string', 'max:255'], 'description' => ['required', 'string'], 'property_id' => ['required', 'integer']]);
        $create->handle(auth()->user()->current_team_id, auth()->id(), [...$data, 'requested_date' => now()->toDateString()]);
        $this->reset(['title', 'description', 'property_id']);
        session()->flash('message', 'Maintenance request submitted successfully.');
        $this->dispatch('maintenanceRequestSubmitted');
    }

    public function render(): View
    {
        return view('real-estate-property-management-livewire::maintenance-request-form');
    }
}
