<?php
declare(strict_types=1);
namespace Liberu\RealEstate\MediaAndDocuments\Models;
use Illuminate\Database\Eloquent\Model; use Illuminate\Support\Str;
final class DocumentTemplate extends Model { protected $table='real_estate_document_templates'; protected $guarded=['id']; public function getCustomFields():array{preg_match_all('/\{\{\s*([a-zA-Z0-9_]+)\s*\}\}/',$this->content,$m);return array_values(array_unique($m[1]??[]));} public function renderContent(array $values):string{return preg_replace_callback('/\{\{\s*([a-zA-Z0-9_]+)\s*\}\}/',fn($m)=>e((string)($values[$m[1]]??'')),$this->content);} public function generateDocument(array $values):string{return $this->renderContent($values);} }
