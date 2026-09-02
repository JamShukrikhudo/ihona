<?php

declare(strict_types=1);

namespace Liberu\RealEstate\LettingsLivewire\Components;

use Illuminate\Contracts\View\View;
use Liberu\RealEstate\Lettings\Application\CreateRentalApplication;
use Livewire\Component;

final class RentalApplicationForm extends Component
{
    public int|string|null $property_id = null;

    public string $employment_status = '';

    public string $annual_income = '';

    public string $desired_move_in_date = '';

    public string $lease_end_date = '';

    public string $ethereum_address = '';

    public function mount(int|string|null $property = null): void
    {
        $this->property_id = $property;
    }

    public function submit(CreateRentalApplication $create): void
    {
        $data = $this->validate(['property_id' => ['required', 'integer'], 'employment_status' => ['required', 'string', 'max:50'], 'annual_income' => ['nullable', 'numeric', 'min:0'], 'desired_move_in_date' => ['required', 'date', 'after:today'], 'lease_end_date' => ['nullable', 'date', 'after:desired_move_in_date'], 'ethereum_address' => ['nullable', 'string', 'max:255']]);
        $create->handle(auth()->user()->current_team_id, auth()->id(), $data);
        session()->flash('status', 'Rental application submitted.');
        $this->dispatch('rentalApplicationSubmitted');
    }

    public function render(): View
    {
        return view('real-estate-lettings-livewire::rental-application-form');
    }
}
