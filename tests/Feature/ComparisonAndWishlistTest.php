<?php

namespace Tests\Feature;

use App\Livewire\PropertyComparison;
use App\Livewire\WishlistManager;
use App\Models\Favorite;
use App\Models\Property;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * Ticket 11 of the Survey Sheet rollout.
 *
 * Comparison is the one place the mono tabular figures earn their keep most:
 * the disclosure facts become the rows, so two homes line up digit for digit.
 */
class ComparisonAndWishlistTest extends TestCase
{
    use RefreshDatabase;

    private function home(array $attributes = []): Property
    {
        return Property::factory()->create(array_merge([
            'price' => 565000,
            'area_sqft' => 1240,
            'bedrooms' => 3,
            'bathrooms' => 2,
            'year_built' => 1904,
            'status' => 'For Sale',
            'currency' => 'GBP',
            'epc' => ['rating' => 'B', 'score' => 84],
            'transit_score' => 78,
            'list_date' => now()->subDays(46),
        ], $attributes));
    }

    public function test_comparison_lines_up_the_disclosure_facts(): void
    {
        $one = $this->home(['title' => 'Alexandra Road']);
        $two = $this->home(['title' => 'Mill Lane', 'price' => 389950, 'area_sqft' => 958]);

        $html = Livewire::test(PropertyComparison::class, ['propertyIds' => $one->id.','.$two->id])
            ->html();

        foreach (['Price', 'Energy', 'Floor area', 'Per sq ft', 'Listed', 'Built'] as $row) {
            $this->assertStringContainsString($row, $html, "the table is missing the [{$row}] row");
        }

        $this->assertStringContainsString('456', $html, 'price per square foot of the first');
        $this->assertStringContainsString('407', $html, 'price per square foot of the second');
        $this->assertStringContainsString('bg-epc-b', $html);
    }

    /**
     * Figures that do not line up cannot be compared, which is the whole point
     * of the page.
     */
    public function test_the_figures_are_tabular(): void
    {
        $one = $this->home(['title' => 'Alexandra Road']);

        $html = Livewire::test(PropertyComparison::class, ['propertyIds' => (string) $one->id])->html();

        $this->assertStringContainsString('tabular-nums', $html);
    }

    public function test_a_fact_a_property_lacks_says_so(): void
    {
        $one = $this->home(['title' => 'No Certificate', 'epc' => null, 'energy_rating' => null]);

        $html = Livewire::test(PropertyComparison::class, ['propertyIds' => (string) $one->id])->html();

        $this->assertStringContainsString('Not supplied', $html);
    }

    /**
     * A wide table must scroll inside its own container, never take the page
     * with it.
     */
    public function test_the_table_scrolls_inside_its_own_container(): void
    {
        $one = $this->home();

        $html = Livewire::test(PropertyComparison::class, ['propertyIds' => (string) $one->id])->html();

        $this->assertMatchesRegularExpression('/<div[^>]*overflow-x-auto[^>]*>\s*<table/s', $html);
    }

    public function test_comparing_nothing_invites_a_search(): void
    {
        $html = Livewire::test(PropertyComparison::class, ['propertyIds' => ''])->html();

        $this->assertStringContainsString('Nothing to compare yet', $html);
        $this->assertStringContainsString(route('property.list'), $html);
    }

    /**
     * The rewrite dropped wire:keyup and there is no updated hook, so nothing
     * ever called searchProperties(): typing showed nothing and no home could
     * be added to the comparison at all.
     */
    public function test_typing_in_the_comparison_search_finds_homes(): void
    {
        $this->home(['title' => 'Alexandra Road']);

        Livewire::test(PropertyComparison::class, ['propertyIds' => ''])
            ->set('searchTerm', 'Alexandra')
            ->assertSee('Alexandra Road');
    }

    public function test_a_found_home_can_be_added_to_the_comparison(): void
    {
        $property = $this->home(['title' => 'Alexandra Road']);

        Livewire::test(PropertyComparison::class, ['propertyIds' => ''])
            ->set('searchTerm', 'Alexandra')
            ->call('addProperty', $property->id)
            ->assertSee('Floor area')
            ->assertDontSee('Nothing to compare yet');
    }

    /**
     * The rate values carry each listing's own currency, so the row label must
     * not name one.
     */
    /**
     * A home already in the comparison stayed in the dropdown, where clicking
     * it again did nothing at all thanks to the duplicate guard.
     */
    public function test_an_added_home_leaves_the_search_results(): void
    {
        $property = $this->home(['title' => 'Alexandra Road']);

        Livewire::test(PropertyComparison::class, ['propertyIds' => ''])
            ->set('searchTerm', 'Alexandra')
            ->assertSee('Alexandra Road')
            ->call('addProperty', $property->id)
            ->assertSet('searchResults', fn ($results) => count($results) === 0);
    }

    public function test_the_rate_row_does_not_name_a_currency(): void
    {
        $one = $this->home(['title' => 'Euro Home', 'currency' => 'EUR']);

        $html = Livewire::test(PropertyComparison::class, ['propertyIds' => (string) $one->id])->html();

        $this->assertStringContainsString('€', $html, 'the price should be in euros');
        $this->assertDoesNotMatchRegularExpression(
            '/<th[^>]*>\s*£\/sq ft/',
            $html,
            'a euro listing must not sit under a row labelled in pounds'
        );
    }

    public function test_the_wishlist_shows_saved_homes_as_cards(): void
    {
        $user = User::factory()->create();
        $property = $this->home(['title' => 'Saved Home']);
        Favorite::create(['user_id' => $user->id, 'property_id' => $property->id]);

        $html = Livewire::actingAs($user)->test(WishlistManager::class)->html();

        $this->assertStringContainsString('property-card', $html);
        $this->assertStringContainsString('Saved Home', $html);
    }

    /**
     * An empty screen is an invitation to act, not a full stop.
     */
    public function test_an_empty_wishlist_names_the_next_action(): void
    {
        $user = User::factory()->create();

        $html = Livewire::actingAs($user)->test(WishlistManager::class)->html();

        $this->assertStringContainsString('Nothing saved yet', $html);
        $this->assertStringContainsString(route('property.list'), $html);
    }

    /**
     * favoriteProperties() is a belongsToMany through `favorites`, and the
     * default sort joined that table a second time — so every favorites column
     * was ambiguous and the page threw on its own default view.
     */
    public function test_every_sort_order_renders(): void
    {
        $user = User::factory()->create();
        $property = $this->home(['title' => 'Saved Home']);
        Favorite::create(['user_id' => $user->id, 'property_id' => $property->id]);

        foreach (['created_at', 'price', 'title'] as $sort) {
            Livewire::actingAs($user)->test(WishlistManager::class)
                ->set('sortBy', $sort)
                ->assertOk()
                ->assertSee('Saved Home');
        }
    }

    /**
     * The select writes sortBy only, so the direction kept its 'desc' default:
     * sorting by address ran Z to A and by price most-expensive first, with no
     * control able to flip it.
     */
    public function test_sorting_by_address_runs_a_to_z(): void
    {
        $user = User::factory()->create();

        foreach (['Zebra Road', 'Alpha Road'] as $title) {
            $property = $this->home(['title' => $title]);
            Favorite::create(['user_id' => $user->id, 'property_id' => $property->id]);
        }

        $html = Livewire::actingAs($user)->test(WishlistManager::class)
            ->set('sortBy', 'title')
            ->html();

        $this->assertLessThan(
            strpos($html, 'Zebra Road'),
            strpos($html, 'Alpha Road'),
            'A should come before Z'
        );
    }

    public function test_sorting_by_price_runs_cheapest_first(): void
    {
        $user = User::factory()->create();

        foreach ([['Dear House', 900000], ['Cheap House', 100000]] as [$title, $price]) {
            $property = $this->home(['title' => $title, 'price' => $price]);
            Favorite::create(['user_id' => $user->id, 'property_id' => $property->id]);
        }

        $html = Livewire::actingAs($user)->test(WishlistManager::class)
            ->set('sortBy', 'price')
            ->html();

        $this->assertLessThan(strpos($html, 'Dear House'), strpos($html, 'Cheap House'));
    }

    /**
     * An empty page after a removal is not a failed search, and saying so sends
     * the reader to clear a search box that is already empty.
     */
    public function test_an_empty_page_after_a_removal_is_not_blamed_on_the_search(): void
    {
        $user = User::factory()->create();
        $property = $this->home(['title' => 'Only Saved Home']);
        Favorite::create(['user_id' => $user->id, 'property_id' => $property->id]);

        Livewire::actingAs($user)->test(WishlistManager::class)
            ->call('removeFavorite', $property->id)
            ->assertDontSee('match that search')
            ->assertSee('Nothing saved yet');
    }

    /**
     * A hook only fires on a client-side update, so a shared link mounted with
     * the default direction and sorted the wrong way.
     */
    public function test_a_shared_sort_link_sorts_the_right_way(): void
    {
        $user = User::factory()->create();

        foreach (['Zebra Road', 'Alpha Road'] as $title) {
            $property = $this->home(['title' => $title]);
            Favorite::create(['user_id' => $user->id, 'property_id' => $property->id]);
        }

        $html = $this->actingAs($user)->get('/wishlist?sortBy=title')->assertOk()->getContent();

        $this->assertLessThan(
            strpos($html, 'Zebra Road'),
            strpos($html, 'Alpha Road'),
            'a shared link must sort the same way the control does'
        );
    }

    public function test_a_confirmation_does_not_outlive_the_next_interaction(): void
    {
        $user = User::factory()->create();
        $property = $this->home(['title' => 'Saved Home']);
        Favorite::create(['user_id' => $user->id, 'property_id' => $property->id]);

        Livewire::actingAs($user)->test(WishlistManager::class)
            ->call('removeFavorite', $property->id)
            ->assertSee('Removed from your shortlist')
            ->set('search', 'anything')
            ->assertDontSee('Removed from your shortlist');
    }

    public function test_removing_a_home_says_so(): void
    {
        $user = User::factory()->create();
        $property = $this->home(['title' => 'Saved Home']);
        Favorite::create(['user_id' => $user->id, 'property_id' => $property->id]);

        Livewire::actingAs($user)->test(WishlistManager::class)
            ->call('removeFavorite', $property->id)
            ->assertSee('Removed from your shortlist');
    }

    public function test_removing_a_home_from_the_wishlist_takes_effect_at_once(): void
    {
        $user = User::factory()->create();
        $property = $this->home(['title' => 'Saved Home']);
        Favorite::create(['user_id' => $user->id, 'property_id' => $property->id]);

        Livewire::actingAs($user)->test(WishlistManager::class)
            ->assertSee('Saved Home')
            ->call('removeFavorite', $property->id)
            ->assertDontSee('Saved Home')
            ->assertSee('Nothing saved yet');

        $this->assertSame(0, Favorite::count(), 'and it survives a reload');
    }
}
