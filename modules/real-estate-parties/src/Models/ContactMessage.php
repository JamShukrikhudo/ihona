<?php

declare(strict_types=1);

namespace Liberu\RealEstate\Parties\Models;

use Illuminate\Database\Eloquent\Model;

final class ContactMessage extends Model
{
    protected $table = 'real_estate_contact_messages';

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return ['is_read' => 'boolean'];
    }
}
