<?php

namespace Qmas\KeywordAnalytics\Tests\Feature;

use Qmas\KeywordAnalytics\Facade;
use Qmas\KeywordAnalytics\Tests\TestCase;

class AnalysisTest extends TestCase
{
    public function test_keyword_to_short()
    {
        $keyword = 'ab';
        $result = Facade::run($keyword)->getResults();

        $this->assertJson('{}');
    }
}