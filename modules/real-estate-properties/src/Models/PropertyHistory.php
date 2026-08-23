<?php

declare(strict_types=1);

namespace Liberu\RealEstate\Properties\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class PropertyHistory extends Model
{
    protected $table = 'real_estate_property_history';

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return ['changes' => 'array'];
    }

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }
}
