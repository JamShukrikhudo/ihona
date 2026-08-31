<?php

declare(strict_types=1);

namespace Liberu\RealEstate\PropertiesLivewire\Components;

use Illuminate\Contracts\View\View;
use Livewire\Attributes\On;
use Livewire\Component;

final class PropertyPreview extends Component
{
    /** @var array<string, mixed>|null */
    public ?array $property = null;

    /** @param array<string, mixed> $property */
    #[On('previewProperty')]
    public function previewProperty(array $property): void
    {
        $this->property = $property;
    }

    public function render(): View
    {
        return view('real-estate-properties-livewire::property-preview');
    }
}
