<?php

declare(strict_types=1);

namespace Liberu\RealEstate\Properties\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class PropertyFavorite extends Model
{
    protected $table = 'real_estate_property_favorites';

    protected $guarded = ['id'];

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }
}
