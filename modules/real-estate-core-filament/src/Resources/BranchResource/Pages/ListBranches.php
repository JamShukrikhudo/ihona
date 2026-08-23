<?php

declare(strict_types=1);

namespace Liberu\RealEstate\CoreFilament\Resources\BranchResource\Pages;

use Filament\Resources\Pages\ListRecords;
use Liberu\RealEstate\CoreFilament\Resources\BranchResource;

final class ListBranches extends ListRecords
{
    protected static string $resource = BranchResource::class;
}
