<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Liberu\Foundation\Organizations\Models\Team;
use Liberu\RealEstate\Core\Models\Territory;
use Liberu\RealEstate\Properties\Models\Property;

class PropertySeeder extends Seeder
{
    /**
     * Seed a handful of real properties so the public API / frontend
     * integration has actual data to verify against, instead of an empty
     * table. Uses only columns that exist on real_estate_properties —
     * deliberately no has_generator/altitude/water_source/mountain_view/
     * max_guests, which the Filament PropertyResource form renders but
     * which were never added as columns (flagged, out of scope per the
     * ihona.tj plan's explicit decision — see docs/handoffs).
     */
    public function run(): void
    {
        $team = Team::firstOrFail();
        $admin = $team->users()->first();

        foreach ([
            [
                'territory' => 'DUSHANBE',
                'title' => '[TEST] Просторная квартира в центре Душанбе',
                'address' => 'проспект Рудаки, 45',
                'description' => 'Тестовая запись для проверки публичного API. Светлая трёхкомнатная квартира с ремонтом, рядом парк Рудаки.',
                'price' => 850000,
                'bedrooms' => 3,
                'area_sqft' => 78,
                'property_type' => 'apartment',
                'latitude' => 38.5605,
                'longitude' => 68.7891,
            ],
            [
                'territory' => 'PAMIR',
                'title' => '[TEST] Гостевой дом с видом на Памир',
                'address' => 'село Хорог, Ишкашимский тракт',
                'description' => 'Тестовая запись. Традиционный памирский дом для туристов и треккеров.',
                'price' => 250,
                'bedrooms' => 5,
                'area_sqft' => 120,
                'property_type' => 'guesthouse',
                'latitude' => 37.4922,
                'longitude' => 71.5548,
            ],
            [
                'territory' => 'KHUJAND',
                'title' => '[TEST] Хостел для путешественников',
                'address' => 'улица Ленина, 12',
                'description' => 'Тестовая запись. Бюджетное размещение рядом с базаром Панчшанбе.',
                'price' => 90,
                'bedrooms' => 8,
                'area_sqft' => 200,
                'property_type' => 'hostel',
                'latitude' => 40.2847,
                'longitude' => 69.6301,
            ],
            [
                'territory' => 'VAHDAT',
                'title' => '[TEST] Загородный коттедж у водохранилища',
                'address' => 'посёлок Ромит',
                'description' => 'Тестовая запись. Двухэтажный коттедж с садом и баней.',
                'price' => 1200000,
                'bedrooms' => 6,
                'area_sqft' => 210,
                'property_type' => 'cottage',
                'latitude' => 38.6102,
                'longitude' => 69.1873,
            ],
            [
                'territory' => 'BOKHTAR',
                'title' => '[TEST] Коммерческое помещение под магазин',
                'address' => 'центральный рынок, ряд 3',
                'description' => 'Тестовая запись. Торговая точка с отдельным входом.',
                'price' => 560000,
                'bedrooms' => null,
                'area_sqft' => 65,
                'property_type' => 'commercial',
                'latitude' => 37.8351,
                'longitude' => 68.7801,
            ],
        ] as $data) {
            $territory = Territory::query()->forTeam($team->id)->where('code', $data['territory'])->first();

            Property::query()->firstOrCreate(
                ['team_id' => $team->id, 'title' => $data['title']],
                [
                    'territory_id' => $territory?->id,
                    'created_by' => $admin?->id,
                    'address' => $data['address'],
                    'description' => $data['description'],
                    'price' => $data['price'],
                    'currency' => 'TJS',
                    'bedrooms' => $data['bedrooms'],
                    'area_sqft' => $data['area_sqft'],
                    'property_type' => $data['property_type'],
                    'latitude' => $data['latitude'],
                    'longitude' => $data['longitude'],
                    'status' => 'available',
                    'published_at' => now(),
                ],
            );
        }
    }
}
