<?php

declare(strict_types=1);

namespace Liberu\RealEstate\MediaAndDocuments\Models;

use Illuminate\Database\Eloquent\Model;

final class DocumentSignature extends Model
{
    protected $table = 'real_estate_document_signatures';

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return ['verified_at' => 'datetime'];
    }

    public function isVerified(): bool
    {
        return $this->verified_at !== null && hash_equals((string) $this->signature_hash, hash('sha256', (string) $this->signature_data));
    }
}
