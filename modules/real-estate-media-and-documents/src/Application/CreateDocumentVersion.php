<?php
declare(strict_types=1);
namespace Liberu\RealEstate\MediaAndDocuments\Application;
use Liberu\RealEstate\MediaAndDocuments\Models\{MediaDocument,DocumentVersion};
final class CreateDocumentVersion { public function handle(MediaDocument $document,int|string $teamId,int|string $userId,array $data):DocumentVersion { abort_unless((string)$document->team_id===(string)$teamId,404); $version=(int)$document->versions()->max('version')+1; return $document->versions()->create(['team_id'=>$teamId,'version'=>$version,'file_name'=>$data['file_name'],'file_path'=>$data['file_path'],'checksum'=>hash('sha256',$data['file_path']),'notes'=>$data['notes']??null]); } }
