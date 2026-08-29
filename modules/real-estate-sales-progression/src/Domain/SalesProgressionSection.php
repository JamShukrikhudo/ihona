<?php

declare(strict_types=1);

namespace Liberu\RealEstate\SalesProgression\Domain;

enum SalesProgressionSection: string
{
    case Chain = 'chain';
    case Milestones = 'milestones';
    case Professionals = 'professionals';
    case CompletionControls = 'completion_controls';
}
