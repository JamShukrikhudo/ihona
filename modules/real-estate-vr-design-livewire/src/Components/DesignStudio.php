<?php

declare(strict_types=1);

namespace Liberu\RealEstate\VrDesignLivewire\Components;

use Illuminate\Contracts\View\View;
use Illuminate\Validation\Rule;
use Liberu\RealEstate\Properties\Models\Property;
use Liberu\RealEstate\VrDesign\Application\VrDesignService;
use Liberu\RealEstate\VrDesign\Models\VrDesign;
use Livewire\Component;
use Livewire\WithFileUploads;

final class DesignStudio extends Component
{
    use WithFileUploads;

    public int|string $propertyId;

    public ?int $selectedDesignId = null;

    public string $designName = '';

    public string $designDescription = '';

    public ?string $designStyle = null;

    public bool $isPublic = false;

    public string $furnitureCategory = '';

    public string $furnitureType = '';

    public float $furniturePositionX = 0;

    public float $furniturePositionY = 0;

    public float $furniturePositionZ = 0;

    public mixed $thumbnailUpload = null;

    public ?string $message = null;

    public function mount(int|string $propertyId): void
    {
        $this->propertyId = $propertyId;
        $this->property();
    }

    public function saveDesign(VrDesignService $service): void
    {
        $this->validate(['designName' => ['required', 'string', 'max:255'], 'designDescription' => ['nullable', 'string', 'max:1000'], 'designStyle' => ['nullable', 'string', Rule::in(array_keys($service->styles()))]]);
        $user = auth()->user();
        abort_unless($user?->current_team_id !== null, 403);
        $design = $service->create($user->current_team_id, $user->getAuthIdentifier(), $this->propertyId, $this->designName, ['created_via' => 'livewire'], $this->designDescription, $this->designStyle, $this->isPublic);
        $this->selectedDesignId = $design->getKey();
        $this->message = 'VR design created successfully.';
        $this->resetDesignForm();
    }

    public function updateDesign(VrDesignService $service): void
    {
        $design = $this->selectedDesign($service);
        $this->validate(['designName' => ['required', 'string', 'max:255'], 'designDescription' => ['nullable', 'string', 'max:1000'], 'designStyle' => ['nullable', 'string', Rule::in(array_keys($service->styles()))]]);
        $service->update($design, ['name' => $this->designName, 'description' => $this->designDescription, 'style' => $this->designStyle, 'is_public' => $this->isPublic]);
        $this->message = 'VR design updated successfully.';
    }

    public function addFurniture(VrDesignService $service): void
    {
        $design = $this->selectedDesign($service);
        $service->addFurniture($design, $this->furnitureCategory, $this->furnitureType, [$this->furniturePositionX, $this->furniturePositionY, $this->furniturePositionZ]);
        $this->message = 'Furniture added successfully.';
    }

    public function removeFurniture(string $furnitureId, VrDesignService $service): void
    {
        $service->removeFurniture($this->selectedDesign($service), $furnitureId);
        $this->message = 'Furniture removed successfully.';
    }

    public function cloneDesign(VrDesignService $service): void
    {
        $user = auth()->user();
        abort_unless($user?->current_team_id !== null, 403);
        $clone = $service->cloneDesign($this->selectedDesign($service), $user->current_team_id, $user->getAuthIdentifier(), $this->selectedDesign($service)->name.' (Copy)');
        $this->selectedDesignId = $clone->getKey();
        $this->message = 'Design cloned successfully.';
    }

    public function deleteDesign(VrDesignService $service): void
    {
        $service->delete($this->selectedDesign($service));
        $this->selectedDesignId = null;
        $this->message = 'VR design deleted successfully.';
    }

    public function uploadThumbnail(VrDesignService $service): void
    {
        $this->validate(['thumbnailUpload' => ['required', 'image', 'mimes:jpeg,png,jpg', 'max:5120']]);
        $service->uploadThumbnail($this->selectedDesign($service), $this->thumbnailUpload);
        $this->thumbnailUpload = null;
        $this->message = 'Thumbnail uploaded successfully.';
    }

    public function render(VrDesignService $service): View
    {
        $user = auth()->user();
        abort_unless($user?->current_team_id !== null, 403);
        $designs = VrDesign::query()->forTeam($user->current_team_id)->where('property_id', $this->propertyId)->latest()->get();

        return view('real-estate-vr-design-livewire::design-studio', ['property' => $this->property(), 'designs' => $designs, 'selectedDesign' => $this->selectedDesignId ? $service->find($user->current_team_id, $this->selectedDesignId) : null, 'styles' => $service->styles(), 'furnitureCategories' => $service->furnitureCategories()]);
    }

    private function property(): Property
    {
        $teamId = auth()->user()?->current_team_id;
        abort_unless($teamId !== null, 403);

        return Property::query()->forTeam($teamId)->whereKey($this->propertyId)->firstOrFail();
    }

    private function selectedDesign(VrDesignService $service): VrDesign
    {
        $teamId = auth()->user()?->current_team_id;
        abort_unless($teamId !== null && $this->selectedDesignId !== null, 403);

        return $service->find($teamId, $this->selectedDesignId);
    }

    private function resetDesignForm(): void
    {
        $this->designName = '';
        $this->designDescription = '';
        $this->designStyle = null;
        $this->isPublic = false;
    }
}
