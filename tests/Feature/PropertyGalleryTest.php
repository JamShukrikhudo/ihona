<?php

namespace Tests\Feature;

use App\Models\Image;
use App\Models\Property;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

/**
 * Ticket 17 of the Survey Sheet rollout: the detail page's gallery.
 *
 * The page showed exactly one photograph — and hid even that one in dark mode,
 * because the img carried `dark:hidden` with no dark counterpart. The floor
 * plan sat 900 lines further down as a separate widget, which is the one thing
 * a buyer scrolls back up looking for.
 */
class PropertyGalleryTest extends TestCase
{
    use RefreshDatabase;

    private function property(array $attributes = []): Property
    {
        return Property::factory()->create(array_merge([
            'status' => 'For Sale',
            'title' => 'Kendrick Road, Reading RG1',
        ], $attributes));
    }

    private function image(Property $property, array $attributes = []): Image
    {
        return Image::create(array_merge([
            'property_id' => $property->id,
            'team_id' => $property->team_id,
            'type' => 'image',
            // The column default, and what the only writer in the app sets.
            'disk' => 'local',
            'file_path' => 'property-media/'.uniqid().'.jpg',
            'file_name' => 'DSC_4417.jpg',
            'mime_type' => 'image/jpeg',
            'is_public' => true,
        ], $attributes));
    }

    public function test_every_photograph_is_in_the_gallery_not_just_the_first(): void
    {
        $property = $this->property();
        $this->image($property, ['title' => 'Kitchen', 'sort_order' => 2]);
        $this->image($property, ['title' => 'Sitting room', 'sort_order' => 1]);

        $this->assertCount(2, $property->gallery());
    }

    /**
     * The one a buyer scrolls back up looking for. It is a drawing of the
     * property, so it belongs beside the photographs of it.
     */
    public function test_the_floor_plan_is_in_the_gallery(): void
    {
        $property = $this->property();
        $this->image($property, ['type' => 'floorplan', 'title' => 'Ground floor']);

        $kinds = $property->gallery()->pluck('kind')->all();

        $this->assertSame(['floor plan'], $kinds);
    }

    public function test_a_site_plan_is_in_the_gallery_too(): void
    {
        $property = $this->property();
        $this->image($property, ['type' => 'siteplan']);

        $this->assertSame(['site plan'], $property->gallery()->pluck('kind')->all());
    }

    /**
     * A visitor opens a gallery to look at rooms. Plans are reference drawings
     * and go after them, however they were uploaded.
     */
    public function test_photographs_come_before_the_plans(): void
    {
        $property = $this->property();
        $this->image($property, ['type' => 'floorplan', 'sort_order' => 1]);
        $this->image($property, ['type' => 'siteplan', 'sort_order' => 2]);
        $this->image($property, ['type' => 'image', 'sort_order' => 3]);

        $this->assertSame(
            ['photograph', 'floor plan', 'site plan'],
            $property->gallery()->pluck('kind')->all()
        );
    }

    public function test_the_primary_photograph_leads(): void
    {
        $property = $this->property();
        $this->image($property, ['title' => 'Bathroom', 'sort_order' => 1]);
        $this->image($property, ['title' => 'Sitting room', 'sort_order' => 9, 'is_primary' => true]);

        $this->assertSame('Sitting room', $property->gallery()->first()->caption);
    }

    /**
     * "DSC_4417.jpg" tells a buyer nothing and reads as an oversight. A room
     * with no name given is better captioned by what it is than by what the
     * camera called the file.
     */
    public function test_a_room_is_captioned_by_name_never_by_filename(): void
    {
        $property = $this->property();
        $this->image($property, ['title' => 'Kitchen', 'file_name' => 'DSC_4417.jpg']);
        $this->image($property, ['title' => null, 'alt_text' => null, 'file_name' => 'IMG_0092.jpg']);

        $captions = $property->gallery()->pluck('caption')->all();

        $this->assertSame('Kitchen', $captions[0]);
        $this->assertNull($captions[1], 'a photograph with no name given must not fall back to its filename');
    }

    public function test_the_alt_text_names_the_room_when_no_title_was_given(): void
    {
        $property = $this->property();
        $this->image($property, ['title' => null, 'alt_text' => 'Rear garden']);

        $this->assertSame('Rear garden', $property->gallery()->first()->caption);
    }

    public function test_media_the_agency_marked_private_stays_off_the_public_page(): void
    {
        $property = $this->property();
        $this->image($property, ['is_public' => false]);

        $this->assertCount(0, $property->gallery());
    }

    /**
     * The V1 media API stores to the private `local` disk, and `local` is also
     * the column default — so this is where every row written by the app
     * actually lands. That disk has no public URL, so `asset('storage/…')` gave
     * a path that 404s and dropping the row instead hid the whole gallery. The
     * site serves it through a route that checks `is_public` first.
     */
    public function test_a_file_on_the_private_disk_is_served_through_the_gated_route(): void
    {
        $property = $this->property();
        $image = $this->image($property, ['disk' => 'local', 'title' => 'Kitchen']);

        $this->assertSame(
            route('property.media', ['property' => $property->id, 'medium' => $image->image_id]),
            $property->gallery()->first()->url
        );
    }

    public function test_the_gated_route_serves_a_public_file(): void
    {
        Storage::fake('local');
        $property = $this->property();
        $image = $this->image($property, ['disk' => 'local', 'file_path' => 'media/kitchen.jpg']);
        Storage::disk('local')->put('media/kitchen.jpg', 'not-really-a-jpeg');

        $this->get(route('property.media', ['property' => $property->id, 'medium' => $image->image_id]))
            ->assertOk()
            ->assertHeader('content-type', 'image/jpeg');
    }

    /**
     * `is_public` is the agency's own switch, and the route is the only thing
     * standing between a private floor plan and anyone who can count.
     */
    public function test_the_gated_route_refuses_a_private_file(): void
    {
        Storage::fake('local');
        $property = $this->property();
        $image = $this->image($property, ['disk' => 'local', 'is_public' => false]);
        Storage::disk('local')->put($image->file_path, 'x');

        $this->get(route('property.media', ['property' => $property->id, 'medium' => $image->image_id]))
            ->assertNotFound();
    }

    public function test_the_gated_route_refuses_media_belonging_to_another_property(): void
    {
        Storage::fake('local');
        $mine = $this->property();
        $theirs = $this->property(['title' => 'Alexandra Road, Reading RG1']);
        $image = $this->image($theirs, ['disk' => 'local']);
        Storage::disk('local')->put($image->file_path, 'x');

        $this->get(route('property.media', ['property' => $mine->id, 'medium' => $image->image_id]))
            ->assertNotFound();
    }

    public function test_a_row_with_no_file_at_all_is_skipped(): void
    {
        $property = $this->property();
        $this->image($property, ['file_path' => null]);

        $this->assertCount(0, $property->gallery());
    }

    public function test_documents_and_brochures_are_not_photographs(): void
    {
        $property = $this->property();
        $this->image($property, ['type' => 'brochure']);
        $this->image($property, ['type' => 'epc']);

        $this->assertCount(0, $property->gallery());
    }

    public function test_the_gallery_reaches_the_page(): void
    {
        $property = $this->property();
        $this->image($property, ['title' => 'Kitchen']);
        $this->image($property, ['type' => 'floorplan']);

        $page = $this->get('/properties/'.$property->id)->assertOk();

        $page->assertSee('Kitchen');
        $page->assertSee('Floor plan');
        $page->assertDontSee('DSC_4417.jpg');
    }

    /**
     * A gallery of mixed crops reads as a jumble of borrowed pictures. One
     * ratio per context is what makes a set look like one property.
     */
    public function test_one_crop_ratio_across_the_gallery(): void
    {
        $property = $this->property();
        $this->image($property, ['title' => 'Kitchen']);
        $this->image($property, ['title' => 'Sitting room']);
        $this->image($property, ['type' => 'floorplan']);

        $html = $this->get('/properties/'.$property->id)->assertOk()->getContent();

        preg_match_all('~aspect-[a-z0-9/\[\]-]+~', $html, $matches);
        $ratios = array_unique($matches[0]);

        $this->assertNotEmpty($ratios, 'the gallery sets no crop ratio at all');
        $this->assertCount(1, $ratios, 'the gallery mixes crop ratios: '.implode(', ', $ratios));
    }

    /**
     * The lead image carried `dark:hidden` and had no dark counterpart, so the
     * whole photograph disappeared on the night theme.
     */
    public function test_the_photographs_survive_the_dark_theme(): void
    {
        $property = $this->property();
        $this->image($property, ['title' => 'Kitchen']);

        $html = $this->get('/properties/'.$property->id)->assertOk()->getContent();

        $this->assertStringNotContainsString('dark:hidden', $html);
    }

    /**
     * The staff panel uploaded to a collection called `property_images`; the
     * model registers `images` and every public view reads that. A property
     * photographed by staff showed no photograph anywhere on the site.
     */
    public function test_staff_upload_to_the_collection_the_site_reads(): void
    {
        $source = file_get_contents(app_path('Filament/Staff/Resources/Properties/PropertyResource.php'));
        $registered = array_keys((new Property)->getRegisteredMediaCollections()->keyBy('name')->all());

        preg_match_all("/->collection\('([a-z_0-9]+)'\)/", $source, $matches);

        foreach (array_unique($matches[1]) as $collection) {
            $this->assertContains(
                $collection,
                $registered,
                "the staff panel uploads to [{$collection}], which the model does not register and nothing reads"
            );
        }
    }

    public function test_a_property_with_no_media_still_renders(): void
    {
        $property = $this->property();

        $this->get('/properties/'.$property->id)->assertOk();
        $this->assertCount(0, $property->gallery());
    }
}
