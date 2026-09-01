<?php

declare(strict_types=1);

namespace Liberu\RealEstate\Viewings\Models;

use Illuminate\Database\Eloquent\Model;

final class ViewingFeedback extends Model
{
    protected $table = 'real_estate_viewing_feedback';

    protected $guarded = ['id'];

    protected function casts(): array
    {
        return ['feedback_submitted_at' => 'datetime', 'would_make_offer' => 'boolean'];
    }

    public function hasBeenSubmitted(): bool
    {
        return $this->feedback_submitted_at !== null;
    }

    public function getAverageRating(): float
    {
        return round(collect([$this->overall_rating, $this->price_rating, $this->condition_rating])->filter(fn ($v) => $v !== null)->avg(), 1);
    }
}
