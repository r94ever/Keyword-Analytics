<?php

namespace Qmas\KeywordAnalytics\Tests;

use Qmas\KeywordAnalytics\KeywordAnalyticsServiceProvider;

class TestCase extends \PHPUnit\Framework\TestCase
{
    protected function getPackageProviders($app): array
    {
        return [
            KeywordAnalyticsServiceProvider::class
        ];
    }
}