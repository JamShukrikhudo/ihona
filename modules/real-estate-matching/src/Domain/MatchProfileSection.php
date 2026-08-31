<?php

declare(strict_types=1);

namespace Liberu\RealEstate\Matching\Domain;

enum MatchProfileSection: string
{
    case Requirements = 'requirements';
    case Affordability = 'affordability';
    case Preferences = 'preferences';
    case Scoring = 'scoring';
    case Alerts = 'alerts';
    case Feedback = 'feedback';
    case Exclusions = 'exclusions';
}
