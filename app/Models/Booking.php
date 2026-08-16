<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\User;
use App\Models\Property;
use App\Models\Team;
use App\Events\BookingCancelled;
use App\Events\BookingRescheduled;
use Carbon\Carbon;

class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'date',
        'time',
        'staff_id',
        'user_id',
        'notes',
        'property_id',
        'name',
        'contact',
        'email',
        'team_id',
        'status',
        'visit_type',
        'feedback',
        'calendar_event_id',
        'booking_type',
    ];

    protected static function booted(): void
    {
        // Maintained rather than fillable: it exists only to hold the unique
        // index that stops two people booking one slot, so it must always
        // agree with the date, time and status beside it.
        static::saving(fn (self $booking) => $booking->slot_key = $booking->slotKey());
    }

    /**
     * Null once cancelled, because a cancelled viewing gives its hour back and
     * nulls do not collide in a unique index.
     */
    public function slotKey(): ?string
    {
        if ($this->status === 'cancelled' || blank($this->date) || blank($this->time)) {
            return null;
        }

        return \Carbon\Carbon::parse($this->date)->format('Y-m-d')
            .' '.\Carbon\Carbon::parse($this->time)->format('H:i');
    }

    protected $casts = [
        'date' => 'date',
    ];

    public function getTimeAttribute($value)
    {
        if (empty($value)) {
            return null;
        }
        return Carbon::parse($value);
    }

    public function setTimeAttribute($value)
    {
        $this->attributes['time'] = $value instanceof Carbon ? $value->format('H:i:s') : $value;
    }

    public function scopeVisits($query)
    {
        return $query->where('visit_type', 'property_visit');
    }

    public function hasProvidedFeedback()
    {
        return !empty($this->feedback);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function staff()
    {
        return $this->belongsTo(User::class, 'staff_id');
    }

    public function property()
    {
        return $this->belongsTo(Property::class, 'property_id');
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    public function setStaffIdAttribute($value)
    {
        $this->attributes['staff_id'] = $value ?? $this->getDefaultStaffId();
    }

    private function getDefaultStaffId()
    {
        return User::where('role', 'staff')->first()->id ?? null;
    }

    public function cancel()
    {
        if ($this->canBeCancelled()) {
            $this->status = 'cancelled';
            $this->save();
            event(new BookingCancelled($this));
            return true;
        }
        return false;
    }

    /**
     * A collision with the unique index on the slot, whichever database threw
     * it. MySQL names the constraint, SQLite names the columns, so both are
     * checked — keying on the MySQL name alone left every SQLite deployment
     * showing the generic error for the one case with a message written for it.
     */
    public static function isSlotCollision(\Throwable $e): bool
    {
        return $e instanceof \Illuminate\Database\QueryException
            && (str_contains($e->getMessage(), 'bookings_property_slot_unique')
                || str_contains($e->getMessage(), 'slot_key'));
    }

    /**
     * Whether a live booking already holds that hour on that property.
     *
     * The unique index is still the last word — two writes racing both pass
     * this — but it lets a form say "pick another" instead of letting the
     * database throw at whoever loses.
     */
    public static function slotIsTaken(int $propertyId, $date, $time, ?int $exceptId = null): bool
    {
        if (blank($propertyId) || blank($date) || blank($time)) {
            return false;
        }

        $key = (new self(['date' => $date, 'time' => $time, 'status' => 'confirmed']))->slotKey();

        return self::query()
            ->where('property_id', $propertyId)
            ->where('slot_key', $key)
            ->when($exceptId, fn ($query) => $query->whereKeyNot($exceptId))
            ->exists();
    }

    public function reschedule($newDate, $newTime)
    {
        if (! $this->canBeRescheduled()) {
            return false;
        }

        $this->date = $newDate;
        $this->time = $newTime;

        try {
            $this->save();
        } catch (\Throwable $e) {
            // Went straight to save() before: moving onto an hour someone else
            // already holds threw a raw QueryException and 500'd the page the
            // customer was standing on.
            if (! self::isSlotCollision($e)) {
                throw $e;
            }

            throw \App\Exceptions\SlotAlreadyBooked::make();
        }

        event(new BookingRescheduled($this));

        return true;
    }

    public function canBeCancelled()
    {
        $time = $this->getRawOriginal('time') ?? ($this->time instanceof \Carbon\Carbon ? $this->time->format('H:i:s') : $this->time);
        $cancellationDeadline = Carbon::parse((is_string($this->date) ? $this->date : $this->date->format('Y-m-d')) . ' ' . $time)->subHours(24);
        return Carbon::now()->lt($cancellationDeadline);
    }

    public function canBeRescheduled()
    {
        $time = $this->getRawOriginal('time') ?? ($this->time instanceof \Carbon\Carbon ? $this->time->format('H:i:s') : $this->time);
        $reschedulingDeadline = Carbon::parse((is_string($this->date) ? $this->date : $this->date->format('Y-m-d')) . ' ' . $time)->subHours(48);
        return Carbon::now()->lt($reschedulingDeadline);
    }

    public function scopeActive($query)
    {
        return $query->where('status', '!=', 'cancelled');
    }
}
