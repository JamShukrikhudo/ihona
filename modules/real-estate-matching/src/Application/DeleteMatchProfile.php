<?php
declare(strict_types=1);
namespace Liberu\RealEstate\Matching\Application;
use Liberu\RealEstate\Matching\Models\MatchProfile;
final class DeleteMatchProfile { public function handle(MatchProfile $profile,int|string $teamId):void{abort_unless((string)$profile->team_id===(string)$teamId,404);$profile->delete();} }
