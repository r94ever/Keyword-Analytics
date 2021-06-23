<?php

namespace Qmas\KeywordAnalytics\Tests;

use Qmas\KeywordAnalytics\KeywordAnalyticsServiceProvider;

class TestCase extends \Orchestra\Testbench\TestCase
{
    protected function getPackageProviders($app): array
    {
        return [
            KeywordAnalyticsServiceProvider::class
        ];
    }

    protected function getEnvironmentSetUp($app)
    {

    }

    protected function setUp(): void
    {
        parent::setUp();
    }
}