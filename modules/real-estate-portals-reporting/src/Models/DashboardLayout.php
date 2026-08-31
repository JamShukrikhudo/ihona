<?php
declare(strict_types=1);
namespace Liberu\RealEstate\PortalsReporting\Models;
use Illuminate\Database\Eloquent\Model;
final class DashboardLayout extends Model { protected $table='real_estate_dashboard_layouts'; protected $guarded=['id']; protected function casts():array{return ['widgets'=>'array','user_id'=>'integer'];} }
