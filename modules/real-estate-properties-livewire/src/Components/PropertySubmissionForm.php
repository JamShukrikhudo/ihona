<?php

declare(strict_types=1);

namespace Liberu\RealEstate\PropertiesLivewire\Components;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Liberu\RealEstate\MediaAndDocuments\Application\CreateMediaDocument;
use Liberu\RealEstate\Properties\Application\CreateProperty;
use Liberu\RealEstate\Properties\Application\GeneratePropertyDescription;
use Liberu\RealEstate\Properties\Models\Property;
use Livewire\Component;
use Livewire\WithFileUploads;

final class PropertySubmissionForm extends Component
{
    use WithFileUploads;

    public string $title = '';

    public string $description = '';

    public string $location = '';

    public int|float|string|null $price = null;

    public int|string|null $bedrooms = null;

    public int|string|null $bathrooms = null;

    public int|float|string|null $area_sqft = null;

    public int|string|null $year_built = null;

    public string $property_type = '';

    /** @var array<int, mixed> */
    public array $images = [];

    public mixed $video = null;

    public string $customDescription = '';

    public ?string $aiDescription = null;

    public string $descriptionTone = 'professional';

    /** @var array<string, string> */
    protected $messages = [];

    /** @return array<string, mixed> */
    protected function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'location' => ['required', 'string', 'max:255'],
            'price' => ['required', 'numeric', 'min:0'],
            'bedrooms' => ['required', 'integer', 'min:0'],
            'bathrooms' => ['required', 'integer', 'min:0'],
            'area_sqft' => ['required', 'numeric', 'min:0'],
            'year_built' => array_merge(['required'], Property::yearBuiltRules()),
            'property_type' => ['required', 'string', 'max:255'],
            'images.*' => ['image', 'max:5120'],
            'video' => ['nullable', 'mimetypes:video/mp4,video/quicktime', 'max:102400'],
            'customDescription' => ['nullable', 'string', 'max:1000'],
            'descriptionTone' => ['required', 'in:professional,casual,luxury'],
        ];
    }

    public function generateAIDescription(GeneratePropertyDescription $generateDescription): void
    {
        $this->validate([
            'location' => ['required', 'string', 'max:255'],
            'price' => ['required', 'numeric', 'min:0'],
            'bedrooms' => ['required', 'integer', 'min:0'],
            'bathrooms' => ['required', 'integer', 'min:0'],
            'area_sqft' => ['required', 'numeric', 'min:0'],
            'property_type' => ['required', 'string', 'max:255'],
            'descriptionTone' => ['required', 'in:professional,casual,luxury'],
        ]);

        $this->aiDescription = $generateDescription->handle([
            'property_type' => $this->property_type,
            'bedrooms' => $this->bedrooms,
            'bathrooms' => $this->bathrooms,
            'area_sqft' => $this->area_sqft,
            'location' => $this->location,
            'price' => $this->price,
        ], $this->descriptionTone);
        $this->description = $this->aiDescription;
    }

    public function updateDescription(string $newDescription): void
    {
        $this->description = $newDescription;
    }

    public function submit(CreateProperty $createProperty, CreateMediaDocument $createMediaDocument): void
    {
        $this->validate();
        $user = Auth::user();
        $teamId = $user?->current_team_id;

        if (! $user || ! $teamId) {
            throw ValidationException::withMessages(['submission' => 'Select a team before submitting a property.']);
        }

        $property = $createProperty->handle($teamId, $user->getAuthIdentifier(), [
            'title' => $this->title,
            'description' => $this->description,
            'description_generated_at' => $this->description === $this->aiDescription && filled($this->aiDescription) ? now() : null,
            'address' => $this->location,
            'price' => $this->price,
            'bedrooms' => $this->bedrooms,
            'bathrooms' => $this->bathrooms,
            'area_sqft' => $this->area_sqft,
            'year_built' => $this->year_built,
            'property_type' => $this->property_type,
            'internal_notes' => $this->customDescription,
        ]);

        foreach ($this->images as $image) {
            $path = $image->store('properties/'.$property->getKey(), 'public');
            $createMediaDocument->handle($teamId, $user->getAuthIdentifier(), [
                'property_id' => $property->getKey(),
                'kind' => 'photo',
                'path' => $path,
            ]);
        }

        if ($this->video) {
            $path = $this->video->store('properties/'.$property->getKey(), 'public');
            $createMediaDocument->handle($teamId, $user->getAuthIdentifier(), [
                'property_id' => $property->getKey(),
                'kind' => 'video',
                'path' => $path,
            ]);
        }

        session()->flash('message', 'Property submitted successfully and is pending approval.');
        $this->reset();
        $this->descriptionTone = 'professional';
    }

    public function preview(): void
    {
        $this->validate();
        $this->dispatch('previewProperty', property: [
            'title' => $this->title,
            'description' => $this->description,
            'location' => $this->location,
            'price' => $this->price,
            'bedrooms' => $this->bedrooms,
            'bathrooms' => $this->bathrooms,
            'area_sqft' => $this->area_sqft,
            'year_built' => $this->year_built,
            'property_type' => $this->property_type,
            'custom_description' => $this->customDescription,
        ]);
    }

    public function render(): View
    {
        return view('real-estate-properties-livewire::property-submission-form');
    }
}
