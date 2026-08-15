<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * The staff panel uploaded property photographs to a media collection called
 * `property_images`. The model registers `images`, and every public view — the
 * card, the detail page, the brochure, the preview — reads `images`. Nothing
 * has ever read `property_images`, so a property photographed by staff showed
 * no photograph anywhere on the site.
 *
 * The panel now writes to `images`. The files already uploaded are still on
 * disk with the wrong label on them; this puts the label right. Conversions and
 * responsive images are keyed on the media id, not the collection name, so
 * nothing needs regenerating.
 */
return new class extends Migration
{
    public function up(): void
    {
        $moved = DB::table('media')
            ->where('model_type', \App\Models\Property::class)
            ->where('collection_name', 'property_images')
            ->update(['collection_name' => 'images']);

        if ($moved > 0) {
            $message = "Moved {$moved} property photograph(s) from the 'property_images' collection to 'images', "
                .'where the public pages read them. They were uploaded but never shown.';

            logger()->warning($message);

            if (app()->runningInConsole()) {
                echo PHP_EOL.'  '.$message.PHP_EOL;
            }
        }
    }

    public function down(): void
    {
        // Every photograph is in `images` now, including ones uploaded before
        // this migration ran through the collection the model registers.
        // Sending them all back would hide those too, so this does not move
        // anything: the panel pointing at `images` is the state to revert.
    }
};
