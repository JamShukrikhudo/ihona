<?php

namespace Tests\Feature;

use App\Livewire\PropertySubmissionForm;
use App\Models\Property;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * Ticket 15 of the Survey Sheet rollout: year built could not hold anything
 * before 1901.
 *
 * `year_built` was a MySQL YEAR column, whose range is 1901-2155. Under this
 * project's sql_mode (STRICT_TRANS_TABLES) a Victorian build year was rejected
 * outright — "Out of range value for column 'year_built'" — and without strict
 * mode it would have stored 0000. That excludes a large share of UK housing
 * stock, and "Built" is one of the five facts on every card.
 *
 * Note the tests run on SQLite, where `year()` is already an integer column, so
 * the round-trip below passes either way. The schema assertion is what actually
 * pins the fix, and it only has something to check on MySQL.
 */
class PeriodPropertyTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_column_is_not_a_mysql_year(): void
    {
        if (Schema::getConnection()->getDriverName() !== 'mysql') {
            $this->markTestSkipped('YEAR is a MySQL type; SQLite already stores this as an integer.');
        }

        $type = collect(Schema::getColumns('properties'))->firstWhere('name', 'year_built')['type_name'];

        $this->assertNotSame('year', $type, 'YEAR cannot hold a build year before 1901');
    }

    public function test_a_victorian_build_year_survives_the_round_trip(): void
    {
        $property = Property::factory()->create(['year_built' => 1861]);

        $this->assertSame(1861, (int) $property->fresh()->year_built);
    }

    public function test_a_period_year_reaches_the_card(): void
    {
        $property = Property::factory()->create([
            'year_built' => 1861,
            'status' => 'For Sale',
            'title' => 'Whitchurch Road, Pangbourne RG8',
        ]);

        $this->get('/properties')->assertOk()->assertSee('1861');
        $this->get('/properties/'.$property->id)->assertOk()->assertSee('1861');
    }

    /**
     * @return array<string, array{int|string, bool}>
     */
    public static function years(): array
    {
        return [
            'a Norman hall' => [1180, true],
            'a Georgian terrace' => [1780, true],
            'a Victorian cottage' => [1861, true],
            'a new build' => [2026, true],
            'the year zero' => [0, false],
            'implausibly early' => [814, false],
            'far in the future' => [2140, false],
        ];
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('years')]
    public function test_validation_accepts_a_real_build_year(int $year, bool $accepted): void
    {
        $form = Livewire::test(PropertySubmissionForm::class)
            ->set('year_built', $year)
            ->call('preview');

        $accepted
            ? $form->assertHasNoErrors('year_built')
            : $form->assertHasErrors('year_built');
    }

    public function test_the_rejection_names_the_range_it_will_accept(): void
    {
        $message = Livewire::test(PropertySubmissionForm::class)
            ->set('year_built', 814)
            ->call('preview')
            ->errors()
            ->first('year_built');

        $this->assertStringContainsString((string) Property::EARLIEST_YEAR_BUILT, $message);
        $this->assertStringContainsString((string) Property::latestYearBuilt(), $message);
    }

    /**
     * The filter is a floor, so widening the column has to widen what it can
     * reach: a search from 1800 has to find an 1861 cottage.
     */
    public function test_the_filter_reaches_below_1901(): void
    {
        Property::factory()->create(['year_built' => 1861, 'status' => 'For Sale']);
        Property::factory()->create(['year_built' => 1994, 'status' => 'For Sale']);

        $this->assertSame(2, Property::where('year_built', '>=', 1800)->count());
        $this->assertSame(1, Property::where('year_built', '>=', 1900)->count());
    }
}
