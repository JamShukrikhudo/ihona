<?php

declare(strict_types=1);

namespace Liberu\RealEstate\PartiesLivewire\Components;

final class TenantReviewForm extends PartyReviewForm
{
    public int|string $tenantId;

    public function mount(int|string $tenantId): void
    {
        $this->tenantId = $tenantId;
        parent::mount($tenantId);
    }
}
