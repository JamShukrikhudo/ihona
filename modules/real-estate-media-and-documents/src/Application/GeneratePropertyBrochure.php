<?php

declare(strict_types=1);

namespace Liberu\RealEstate\MediaAndDocuments\Application;

use Illuminate\Validation\ValidationException;

final class GeneratePropertyBrochure
{
    /** @param array<string, mixed> $property @param array<string, mixed> $options @return array<string, mixed> */
    public function handle(array $property, array $options = []): array
    {
        foreach (['id', 'title', 'price'] as $required) {
            if (! array_key_exists($required, $property)) {
                throw ValidationException::withMessages(['property' => "The property {$required} field is required."]);
            }
        }
        $data = ['template' => $options['template'] ?? 'standard', 'property' => ['id' => $property['id'], 'title' => (string) $property['title'], 'description' => (string) ($property['description'] ?? ''), 'location' => (string) ($property['location'] ?? $property['address'] ?? ''), 'price' => $property['price'], 'formatted_price' => '£'.number_format((float) $property['price'], 0), 'bedrooms' => $property['bedrooms'] ?? null, 'bathrooms' => $property['bathrooms'] ?? null, 'area_sqft' => $property['area_sqft'] ?? null, 'property_type' => $property['property_type'] ?? null, 'energy_rating' => $property['energy_rating'] ?? null, 'energy_score' => $property['energy_score'] ?? null, 'features' => array_values($property['features'] ?? []), 'images' => array_values($property['images'] ?? [])], 'options' => ['include_floor_plan' => $options['include_floor_plan'] ?? true, 'include_map' => $options['include_map'] ?? true, 'include_epc' => $options['include_epc'] ?? true], 'generated_at' => now()->toDateTimeString()];
        $escape = fn (mixed $value): string => htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
        $features = implode('', array_map(fn (mixed $feature): string => '<li>'.$escape($feature).'</li>', $data['property']['features']));
        $images = implode('', array_map(fn (mixed $image): string => '<img src="'.$escape($image).'" style="max-width:100%;margin-bottom:10px" alt="Property image">', array_slice($data['property']['images'], 0, 6)));
        $title = $escape($data['property']['title']);
        $description = $escape($data['property']['description']);
        $location = $escape($data['property']['location']);
        $html = '<!doctype html><html><head><meta charset="UTF-8"><title>'.$title.' - Property Brochure</title></head><body><h1>'.$title.'</h1><p><strong>'.$data['property']['formatted_price'].'</strong></p><p><strong>Location:</strong> '.$location.'</p><p>'.$data['property']['bedrooms'].' bedrooms · '.$data['property']['bathrooms'].' bathrooms · '.$data['property']['area_sqft'].' sq ft</p><p>'.$description.'</p><h2>Features</h2><ul>'.$features.'</ul><h2>Photos</h2>'.$images.'</body></html>';

        return ['data' => $data, 'html' => $html];
    }
}
