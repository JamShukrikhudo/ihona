<?php

use App\Models\Booking;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Booking a viewing checked the slot was free and then inserted, with nothing
 * between the two. Two visitors submitting 14:00 at the same moment both
 * passed the check and both rows were written — two people at one viewing.
 *
 * The key is null for a cancelled booking rather than the slot itself: a
 * cancelled viewing gives its hour back, which the pickers already honour, and
 * nulls do not collide in a unique index on either MySQL or SQLite. So the
 * same slot can be booked, cancelled and booked again, any number of times,
 * while only one live booking can hold it.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->string('slot_key', 64)->nullable()->after('time');
        });

        $this->backfill();
        $this->reportExistingClashes();

        Schema::table('bookings', function (Blueprint $table) {
            $table->unique(['property_id', 'slot_key'], 'bookings_property_slot_unique');
        });
    }

    private function backfill(): void
    {
        Booking::query()
            ->whereNot('status', 'cancelled')
            ->orderBy('id')
            ->chunkById(200, function ($bookings) {
                foreach ($bookings as $booking) {
                    $booking->slot_key = $booking->slotKey();
                    $booking->saveQuietly();
                }
            });
    }

    /**
     * Rows that already double-booked a slot cannot all keep their key, or the
     * index will not build. The oldest booking holds it — it was there first —
     * and the rest are named so someone can call those people back.
     */
    private function reportExistingClashes(): void
    {
        $clashes = DB::table('bookings')
            ->select('property_id', 'slot_key', DB::raw('COUNT(*) as total'), DB::raw('MIN(id) as keep'))
            ->whereNotNull('slot_key')
            ->groupBy('property_id', 'slot_key')
            ->havingRaw('COUNT(*) > 1')
            ->get();

        if ($clashes->isEmpty()) {
            return;
        }

        foreach ($clashes as $clash) {
            DB::table('bookings')
                ->where('property_id', $clash->property_id)
                ->where('slot_key', $clash->slot_key)
                ->where('id', '!=', $clash->keep)
                ->update(['slot_key' => null]);
        }

        $message = 'Two or more viewings already share a slot: '
            .$clashes->map(fn ($c) => 'property '.$c->property_id.' at '.$c->slot_key.' ('.$c->total.' bookings)')->implode(', ')
            .'. The earliest keeps the slot; the others are still in the diary and need someone to call them.';

        logger()->warning($message);

        if (app()->runningInConsole()) {
            echo PHP_EOL.'  '.$message.PHP_EOL;
        }
    }

    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropUnique('bookings_property_slot_unique');
            $table->dropColumn('slot_key');
        });
    }
};
