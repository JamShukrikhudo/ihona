<?php

declare(strict_types=1);

namespace Liberu\RealEstate\PartiesLivewire\Components;

final class LandlordReviewForm extends PartyReviewForm
{
    public int|string $landlordId;

    public function mount(int|string $landlordId): void
    {
        $this->landlordId = $landlordId;
        parent::mount($landlordId);
    }
}
