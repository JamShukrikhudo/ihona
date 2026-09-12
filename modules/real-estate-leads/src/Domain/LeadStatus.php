<?php

declare(strict_types=1);

namespace Liberu\RealEstate\Leads\Domain;

enum LeadStatus: string
{
    case New = 'new';
    case Contacted = 'contacted';
    case Qualified = 'qualified';
    case Viewing = 'viewing';
    case Offer = 'offer';
    case Won = 'won';
    case Lost = 'lost';
}
