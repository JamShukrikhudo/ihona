<?php

namespace Tests\Feature;

use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class ContentPagesTest extends TestCase
{
    /** @return array<string, array{string, string}> */
    public static function readingPages(): array
    {
        return [
            'about' => ['/about', 'about.blade.php'],
            'services' => ['/services', 'services.blade.php'],
            'terms' => ['/terms-and-conditions', 'terms-and-conditions.blade.php'],
            'privacy' => ['/privacy', 'privacy-policy.blade.php'],
        ];
    }

    #[DataProvider('readingPages')]
    public function test_a_public_reading_page_is_available_and_uses_the_design_system(string $uri, string $view): void
    {
        $response = $this->get($uri);

        $response->assertOk()->assertSee('max-w-reading');

        $source = file_get_contents(resource_path('views/'.$view));

        $this->assertIsString($source);
        $this->assertStringNotContainsString('bg-white', $source);
        $this->assertStringNotContainsString('text-gray-', $source);
    }

    #[DataProvider('readingPages')]
    public function test_a_public_reading_page_has_one_h1_and_no_skipped_heading_level(string $uri, string $view): void
    {
        $html = $this->get($uri)->assertOk()->getContent();

        preg_match_all('/<h([1-6])\b/', $html, $matches);
        $levels = array_map('intval', $matches[1]);

        $this->assertSame(1, count(array_filter($levels, fn (int $level): bool => $level === 1)));

        $previous = 0;
        foreach ($levels as $level) {
            if ($previous !== 0) {
                $this->assertLessThanOrEqual($previous + 1, $level);
            }

            $previous = $level;
        }
    }
}
