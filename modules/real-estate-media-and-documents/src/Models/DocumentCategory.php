<?php
declare(strict_types=1);
namespace Liberu\RealEstate\MediaAndDocuments\Models;
use Illuminate\Database\Eloquent\Builder; use Illuminate\Database\Eloquent\Model; use Illuminate\Database\Eloquent\Relations\BelongsToMany;
final class DocumentCategory extends Model { protected $table='real_estate_document_categories'; protected $guarded=['id']; public function scopeForTeam(Builder $q,int|string $id):Builder{return $q->where('team_id',$id);} public function documents():BelongsToMany{return $this->belongsToMany(MediaDocument::class,'real_estate_document_category_media');} }
