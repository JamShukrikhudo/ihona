<?php

namespace Tests\Feature;

use App\Livewire\CalculatorsComponent;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * Ticket 10 of the Survey Sheet rollout: the pages a visitor reads rather than
 * operates.
 *
 * Running copy gets a real measure and the type scale, not default prose.
 */
class ContentPagesTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @return array<string, array{string}>
     */
    public static function readingPages(): array
    {
        return [
            'about' => ['/about'],
            'services' => ['/services'],
            'terms' => ['/terms-and-conditions'],
            'privacy' => ['/privacy'],
        ];
    }

    private const VIEWS = [
        '/about' => 'about.blade.php',
        '/services' => 'services.blade.php',
        '/terms-and-conditions' => 'terms-and-conditions.blade.php',
        '/privacy' => 'privacy-policy.blade.php',
    ];

    #[\PHPUnit\Framework\Attributes\DataProvider('readingPages')]
    public function test_a_reading_page_uses_the_system(string $uri): void
    {
        $this->get($uri)->assertOk();

        // The page's own source, not the whole document: shared chrome belongs
        // to its own component, and a failure should name the file to change.
        $source = file_get_contents(resource_path('views/'.self::VIEWS[$uri]));

        $this->assertStringNotContainsString('bg-white', $source, "[{$uri}] hardcodes a white surface");
        $this->assertStringNotContainsString('text-gray-', $source, "[{$uri}] uses the legacy ink scale");
    }

    /**
     * Running copy needs a real measure. A paragraph that runs the full width
     * of a 1440px sheet is not read, it is skimmed and abandoned.
     */
    #[\PHPUnit\Framework\Attributes\DataProvider('readingPages')]
    public function test_a_reading_page_constrains_its_measure(string $uri): void
    {
        $html = $this->get($uri)->assertOk()->getContent();

        $this->assertStringContainsString('max-w-reading', $html, "[{$uri}] has no reading measure");
    }

    /**
     * One h1, and no level skipped: a heading outline is how anyone using a
     * screen reader navigates a long page.
     */
    #[\PHPUnit\Framework\Attributes\DataProvider('readingPages')]
    public function test_a_reading_page_has_a_sound_heading_outline(string $uri): void
    {
        $html = $this->get($uri)->assertOk()->getContent();

        preg_match_all('/<h([1-6])\b/', $html, $matches);
        $levels = array_map('intval', $matches[1]);

        $this->assertSame(
            1,
            count(array_filter($levels, fn ($l) => $l === 1)),
            "[{$uri}] should have exactly one h1"
        );

        $previous = 0;

        foreach ($levels as $level) {
            if ($previous !== 0) {
                $this->assertLessThanOrEqual(
                    $previous + 1,
                    $level,
                    "[{$uri}] jumps from h{$previous} to h{$level}"
                );
            }

            $previous = $level;
        }
    }

    public function test_the_news_list_uses_the_system(): void
    {
        $html = $this->get('/news')->assertOk()->getContent();

        $this->assertStringContainsString('font-display', $html);
        $this->assertStringNotContainsString('bg-white', $html);
    }

    public function test_an_empty_news_list_names_the_next_action(): void
    {
        $html = $this->get('/news')->assertOk()->getContent();

        $this->assertStringContainsString('No stories yet', $html);
        $this->assertStringContainsString(route('property.list'), $html);
    }

    /**
     * Figures a reader compares have to line up, and the assumptions behind a
     * calculated number have to be stated or the number is a guess with a
     * confident font.
     */
    /**
     * Driven, not just fetched: a result block only renders once something has
     * been calculated, so an assertion against the empty page proves nothing
     * about how a figure is presented.
     */
    public function test_a_calculated_figure_lines_up_and_states_its_assumptions(): void
    {
        $html = Livewire::test(CalculatorsComponent::class)
            ->set('propertyPrice', 400000)
            ->set('loanAmount', 300000)
            ->set('interestRate', 4.5)
            ->set('loanTerm', 25)
            ->call('calculateMortgage')
            ->html();

        $this->assertStringContainsString('tabular-nums', $html, 'figures must line up');
        $this->assertStringContainsString('Assumes:', $html, 'a calculated figure must say what it assumed');
        $this->assertMatchesRegularExpression('/Monthly Payment/i', $html);
    }

    public function test_the_calculators_page_uses_the_system(): void
    {
        $source = file_get_contents(resource_path('views/livewire/calculators.blade.php'));

        $this->assertStringNotContainsString('bg-white', $source);
        $this->assertStringNotContainsString('text-gray-', $source);
    }

    public function test_the_calculators_have_a_sound_heading_outline(): void
    {
        $html = $this->get('/calculators')->assertOk()->getContent();

        preg_match_all('/<h([1-6])\b/', $html, $matches);

        $this->assertSame(
            1,
            count(array_filter(array_map('intval', $matches[1]), fn ($l) => $l === 1)),
            'the calculators page should have exactly one h1'
        );
    }
}
