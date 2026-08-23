<?php
declare(strict_types=1);
namespace Liberu\RealEstate\Offers\Application;
use Illuminate\Validation\ValidationException; use Liberu\RealEstate\Offers\Models\Offer;
final class UpdateOffer { public function handle(Offer $offer,int|string $teamId,array $attributes):Offer{abort_unless((string)$offer->team_id===(string)$teamId,404);if(array_key_exists('subject',$attributes)&&trim((string)$attributes['subject'])===''){throw ValidationException::withMessages(['subject'=>'An offer subject is required.']);}$offer->fill($attributes);$offer->save();return $offer->fresh();} }
