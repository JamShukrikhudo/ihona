<?php
declare(strict_types=1);
namespace Liberu\RealEstate\Offers\Domain;
enum OfferStatus:string { case Draft='draft'; case Submitted='submitted'; case Countered='countered'; case Accepted='accepted'; case Rejected='rejected'; case Withdrawn='withdrawn'; }
