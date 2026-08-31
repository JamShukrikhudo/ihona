<?php

declare(strict_types=1);

namespace Liberu\RealEstate\Lettings\Domain;

enum LeaseAgreementStatus: string
{
    case Draft = 'draft';
    case PendingSignature = 'pending_signature';
    case Active = 'active';
    case NoticeServed = 'notice_served';
    case Ended = 'ended';
    case Terminated = 'terminated';
    case Renewed = 'renewed';
}
