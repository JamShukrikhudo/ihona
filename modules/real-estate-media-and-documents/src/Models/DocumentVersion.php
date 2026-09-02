<?php

declare(strict_types=1);

namespace Liberu\RealEstate\MediaAndDocuments\Models;

use Illuminate\Database\Eloquent\Model;

final class DocumentVersion extends Model
{
    protected $table = 'real_estate_document_versions';

    protected $guarded = ['id'];
}
