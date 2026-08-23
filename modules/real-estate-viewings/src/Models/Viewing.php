<?php
declare(strict_types=1);
namespace Liberu\RealEstate\Viewings\Models;
use Illuminate\Database\Eloquent\Model; use Illuminate\Database\Eloquent\SoftDeletes; use Liberu\RealEstate\Viewings\Domain\ViewingStatus;
final class Viewing extends Model { use SoftDeletes; protected $table='real_estate_viewings'; protected $guarded=['id']; protected function casts():array{return ['status'=>ViewingStatus::class,'access'=>'array','accompaniment'=>'array','reminders'=>'array','feedback'=>'array','starts_at'=>'datetime','ends_at'=>'datetime'];} public function scopeForTeam($query,int|string $teamId){return $query->where('team_id',$teamId);} }
