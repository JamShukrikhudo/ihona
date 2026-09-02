<?php

declare(strict_types=1);

namespace Liberu\RealEstate\PropertyManagementFilament\Resources\VendorQuoteResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Liberu\RealEstate\PropertyManagement\Application\CreateVendorQuote as CreateVendorQuoteAction;
use Liberu\RealEstate\PropertyManagementFilament\Resources\VendorQuoteResource;

final class CreateVendorQuote extends CreateRecord
{
    protected static string $resource = VendorQuoteResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        $user = auth()->user();
        abort_unless($user?->current_team_id !== null, 403);

        return app(CreateVendorQuoteAction::class)->handle($user->current_team_id, $user->getAuthIdentifier(), [...$data, 'quote_date' => $data['quote_date'] ?? now()->toDateString(), 'valid_until' => $data['valid_until'] ?? now()->addDays(30)->toDateString()]);
    }
}
