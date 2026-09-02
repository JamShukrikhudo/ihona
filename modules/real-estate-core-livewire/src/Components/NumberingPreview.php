<?php

declare(strict_types=1);

namespace Liberu\RealEstate\CoreLivewire\Components;

use Liberu\RealEstate\Core\Application\NextNumber;
use Livewire\Component;

final class NumberingPreview extends Component
{
    public string $key = 'property';

    public string $prefix = '';

    public int $padding = 6;

    public ?string $number = null;

    public function generate(NextNumber $nextNumber): void
    {
        $teamId = auth()->user()?->current_team_id;
        abort_unless($teamId !== null, 403);
        $this->number = $nextNumber->handle($teamId, $this->key, $this->prefix !== '' ? $this->prefix : null, $this->padding);
    }

    public function render(): mixed
    {
        return view('real-estate-core-livewire::numbering-preview');
    }
}
