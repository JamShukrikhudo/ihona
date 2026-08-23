<?php
declare(strict_types=1);
namespace Liberu\RealEstate\Listings\Domain;
enum ListingStatus:string { case Draft='draft'; case Ready='ready'; case Published='published'; case Suspended='suspended'; case Withdrawn='withdrawn'; }
