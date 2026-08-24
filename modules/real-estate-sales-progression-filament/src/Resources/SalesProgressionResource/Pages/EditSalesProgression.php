<?php

declare(strict_types=1);

declare(strict_types=1);

namespace Liberu\RealEstate\SalesProgressionFilament\Resources\SalesProgressionResource\Pages;

use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use Liberu\RealEstate\SalesProgression\Application\UpdateSalesProgression;
use Liberu\RealEstate\SalesProgressionFilament\Resources\SalesProgressionResource;

final class EditSalesProgression extends EditRecord
{
    protected static string $resource = SalesProgressionResource::class;

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $teamId = auth()->user()?->current_team_id;
        abort_unless($teamId !== null && (string) $teamId === (string) $record->team_id, 403);

        return app(UpdateSalesProgression::class)->handle($record, $teamId, $data);
    }
}
