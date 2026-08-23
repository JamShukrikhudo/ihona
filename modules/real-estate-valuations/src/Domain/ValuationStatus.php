<?php
declare(strict_types=1);
namespace Liberu\RealEstate\Valuations\Domain;
enum ValuationStatus: string { case Draft='draft'; case Scheduled='scheduled'; case Completed='completed'; case Converted='converted'; case Cancelled='cancelled'; }
