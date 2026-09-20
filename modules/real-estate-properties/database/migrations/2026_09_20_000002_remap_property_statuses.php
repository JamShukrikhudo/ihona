<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * PropertyStatus shrinks from 16 UK-jargon states to 4 publication states
 * (draft/moderation/published/archive) — deal outcome (sold/let/reserved)
 * moves off Property entirely. Confirmed via direct query before writing
 * this: all 9 live properties are 'available' and there are zero Offer
 * records, so this remap is data-safe in practice; the full old->new
 * table below is still applied defensively in case any other environment
 * has rows in the legacy states.
 */
return new class() extends Migration
{
    private const PUBLISHED = ['available', 'for sale', 'for_sale', 'For Sale', 'for rent', 'for_rent', 'For Rent', 'to_let', 'coming_soon'];

    private const ARCHIVE = ['under_offer', 'sold', 'let', 'withdrawn', 'let_agreed', 'sold_stc', 'sstc', 'exchanged', 'archived', 'rented', 'Rented'];

    public function up(): void
    {
        DB::table('real_estate_properties')->whereIn('status', self::PUBLISHED)->update(['status' => 'published']);
        DB::table('real_estate_properties')->whereIn('status', self::ARCHIVE)->update(['status' => 'archive']);
    }

    public function down(): void
    {
        DB::table('real_estate_properties')->where('status', 'published')->update(['status' => 'available']);
        DB::table('real_estate_properties')->where('status', 'archive')->update(['status' => 'withdrawn']);
    }
};
