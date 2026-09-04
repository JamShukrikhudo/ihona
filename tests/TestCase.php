<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    /**
     * This host also serves live production traffic from the same .env, and Feature
     * tests run under RefreshDatabase — which calls migrate:fresh. A stray cached
     * bootstrap/cache/config.php (built via `php artisan optimize` for prod) makes
     * Laravel skip phpunit.xml's env overrides entirely, so a test run picks up the
     * real production database instead of the sqlite fixture and can drop it. Refuse
     * to boot any test at all unless the environment actually resolved to "testing".
     */
    protected function setUp(): void
    {
        parent::setUp();

        if (! $this->app->environment('testing')) {
            $this->fail(
                'Refusing to run tests: app()->environment() is "'.$this->app->environment().'", not "testing". '.
                'This usually means bootstrap/cache/config.php is cached and is overriding phpunit.xml\'s env vars — run `php artisan config:clear` first.',
            );
        }
    }
}
