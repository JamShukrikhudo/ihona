<?php

declare(strict_types=1);

namespace Liberu\RealEstate\Instructions\Domain;

enum InstructionStatus: string
{
    case Draft = 'draft';
    case PendingApproval = 'pending_approval';
    case Approved = 'approved';
    case Withdrawn = 'withdrawn';
    case Rejected = 'rejected';
}
