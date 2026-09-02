<?php

declare(strict_types=1);

namespace Liberu\RealEstate\Lettings\Domain;

enum RentalApplicationStatus: string
{
    case Draft = 'draft';
    case Submitted = 'submitted';
    case UnderReview = 'under_review';
    case Approved = 'approved';
    case Rejected = 'rejected';
}
