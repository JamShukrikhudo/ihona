<?php

declare(strict_types=1);

namespace Liberu\RealEstate\MediaAndDocuments\Application;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\ValidationException;

final class VerifyDigitalSignature
{
    public function handle(Model $signature, int|string $teamId): bool
    {
        if ((string) $signature->team_id !== (string) $teamId || ! hash_equals((string) $signature->signature_hash, hash('sha256', (string) $signature->signature_data))) {
            throw ValidationException::withMessages(['signature' => 'Signature verification failed.']);
        } $signature->forceFill(['verified_at' => now()])->save();

        return true;
    }
}
