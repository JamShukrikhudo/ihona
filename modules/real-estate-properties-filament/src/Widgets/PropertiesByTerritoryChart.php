<?php

declare(strict_types=1);

namespace Liberu\RealEstate\PropertiesFilament\Widgets;

use Filament\Facades\Filament;
use Filament\Widgets\ChartWidget;
use Liberu\RealEstate\Properties\Models\Property;
use Liberu\RealEstate\Properties\Models\Region;

final class PropertiesByTerritoryChart extends ChartWidget
{
    protected ?string $heading = 'Объекты по регионам';

    protected function getData(): array
    {
        $team = Filament::getTenant();

        if (! $team) {
            return ['datasets' => [], 'labels' => []];
        }

        $counts = Property::query()
            ->forTeam($team->id)
            ->whereNotNull('region_id')
            ->selectRaw('region_id, count(*) as aggregate')
            ->groupBy('region_id')
            ->pluck('aggregate', 'region_id');

        $regionNames = Region::query()
            ->whereIn('id', $counts->keys())
            ->pluck('name', 'id');

        return [
            'datasets' => [[
                'label' => 'Объекты',
                'data' => $counts->values()->all(),
                'backgroundColor' => '#2f7fd6',
            ]],
            'labels' => $counts->keys()->map(fn ($id) => $regionNames[$id] ?? "#{$id}")->all(),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
