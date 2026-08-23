<?php
declare(strict_types=1);
namespace Liberu\RealEstate\Matching\Application;
use Illuminate\Validation\ValidationException; use Liberu\RealEstate\Matching\Models\MatchProfile;
final class UpdateMatchProfile { public function handle(MatchProfile $profile,int|string $teamId,array $attributes):MatchProfile{abort_unless((string)$profile->team_id===(string)$teamId,404);if(array_key_exists('subject',$attributes)&&trim((string)$attributes['subject'])===''){throw ValidationException::withMessages(['subject'=>'A matching profile subject is required.']);}if(array_key_exists('score',$attributes)){$attributes['score']=max(0,min(100,(int)$attributes['score']));}$profile->fill($attributes);$profile->save();return $profile->fresh();} }
