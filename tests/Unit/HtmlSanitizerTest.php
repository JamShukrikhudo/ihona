<?php

namespace Tests\Unit;

use App\Services\HtmlSanitizer;
use Tests\TestCase;

class HtmlSanitizerTest extends TestCase
{
    public function test_it_removes_executable_html_and_unsafe_urls(): void
    {
        $html = '<p onclick="alert(1)">Safe <img src=x onerror=alert(1)></p>'
            .'<script>alert(1)</script><a href="javascript:alert(1)">click</a>';

        $clean = app(HtmlSanitizer::class)->sanitize($html);

        $this->assertStringContainsString('<p>Safe </p>', $clean);
        $this->assertStringNotContainsString('onclick', $clean);
        $this->assertStringNotContainsString('<script', $clean);
        $this->assertStringNotContainsString('javascript:', $clean);
    }
}
