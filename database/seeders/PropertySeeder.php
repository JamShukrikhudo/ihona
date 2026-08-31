<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Liberu\Foundation\Organizations\Models\Team;
use Liberu\RealEstate\Core\Models\Territory;
use Liberu\RealEstate\Properties\Models\Property;

class PropertySeeder extends Seeder
{
    /**
     * Seed a representative catalog so the public API / frontend have real
     * data to verify against, instead of an empty table. Uses only columns
     * that exist on real_estate_properties — deliberately no has_generator/
     * altitude/water_source/mountain_view/max_guests, which the Filament
     * PropertyResource form renders but which were never added as columns
     * (flagged, out of scope per the ihona.tj plan's explicit decision —
     * see docs/handoffs).
     *
     * Weighted toward the business priority: buy/rent city housing (flat,
     * house, land, cottage, commercial) first — tourism (guesthouse,
     * hostel, nightly deal_type=rent) is two entries, not the majority.
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
                'description' => 'Светлая трёхкомнатная квартира с ремонтом, рядом парк Рудаки и деловой центр. Тёплые полы, новая проводка.',
                'price' => 850000,
                'bedrooms' => 3,
                'area_sqft' => 78,
                'property_type' => 'apartment',
                'deal_type' => 'sale',
                'latitude' => 38.5605,
                'longitude' => 68.7891,
            ],
            [
                'territory' => 'DUSHANBE',
                'title' => '[TEST] Уютная 2-комнатная квартира в аренду',
                'address' => 'улица Бухоро, 21',
                'description' => 'Сдаётся длительно, помесячная оплата. Вся мебель и техника включены, рядом школа и поликлиника.',
                'price' => 6500,
                'bedrooms' => 2,
                'area_sqft' => 54,
                'property_type' => 'apartment',
                'deal_type' => 'rent',
                'latitude' => 38.5651,
                'longitude' => 68.774,
            ],
            [
                'territory' => 'KULYAB',
                'title' => '[TEST] Дом с садом в Кулябе',
                'address' => 'улица Исмоили Сомони, 8',
                'description' => 'Семейный дом с фруктовым садом и отдельным гостевым флигелем, документы готовы к сделке.',
                'price' => 940000,
                'bedrooms' => 5,
                'area_sqft' => 160,
                'property_type' => 'house',
                'deal_type' => 'sale',
                'latitude' => 37.9131,
                'longitude' => 69.7845,
            ],
            [
                'territory' => 'TURSUNZADE',
                'title' => '[TEST] Участок под строительство',
                'address' => 'джамоат Навбунёд',
                'description' => 'Ровный участок с подведённым электричеством, документы готовы, рядом асфальтированная дорога.',
                'price' => 320000,
                'bedrooms' => null,
                'area_sqft' => 600,
                'property_type' => 'land',
                'deal_type' => 'sale',
                'latitude' => 38.5109,
                'longitude' => 68.2417,
            ],
            [
                'territory' => 'VAHDAT',
                'title' => '[TEST] Загородный коттедж у водохранилища',
                'address' => 'посёлок Ромит',
                'description' => 'Двухэтажный коттедж с садом, баней и собственным источником воды — готов к продаже.',
                'price' => 1200000,
                'bedrooms' => 6,
                'area_sqft' => 210,
                'property_type' => 'cottage',
                'deal_type' => 'sale',
                'latitude' => 38.6102,
                'longitude' => 69.1873,
            ],
            [
                'territory' => 'BOKHTAR',
                'title' => '[TEST] Коммерческое помещение в аренду',
                'address' => 'центральный рынок, ряд 3',
                'description' => 'Торговая точка с отдельным входом и складским помещением, сдаётся помесячно.',
                'price' => 8500,
                'bedrooms' => null,
                'area_sqft' => 65,
                'property_type' => 'commercial',
                'deal_type' => 'rent',
                'latitude' => 37.8351,
                'longitude' => 68.7801,
            ],
            [
                'territory' => 'PAMIR',
                'title' => '[TEST] Гостевой дом с видом на Памир',
                'address' => 'село Хорог, Ишкашимский тракт',
                'description' => 'Традиционный памирский дом для туристов и треккеров, панорама на хребет. Посуточная аренда.',
                'price' => 250,
                'bedrooms' => 5,
                'area_sqft' => 120,
                'property_type' => 'guesthouse',
                'deal_type' => 'rent',
                'latitude' => 37.4922,
                'longitude' => 71.5548,
            ],
            [
                'territory' => 'KHUJAND',
                'title' => '[TEST] Хостел для путешественников',
                'address' => 'улица Ленина, 12',
                'description' => 'Бюджетное размещение рядом с базаром Панчшанбе, общая кухня и терраса. Посуточная аренда.',
                'price' => 90,
                'bedrooms' => 8,
                'area_sqft' => 200,
                'property_type' => 'hostel',
                'deal_type' => 'rent',
                'latitude' => 40.2847,
                'longitude' => 69.6301,
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
                    'deal_type' => $data['deal_type'],
                    'latitude' => $data['latitude'],
                    'longitude' => $data['longitude'],
                    'status' => 'available',
                    'published_at' => now(),
                ],
            );
        }
    }
}
