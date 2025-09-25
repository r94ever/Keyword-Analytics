<?php

namespace Qmas\KeywordAnalytics\Tests\Unit;

use Qmas\KeywordAnalytics\Helper;
use Qmas\KeywordAnalytics\Tests\TestCase;

class HelperTest extends TestCase
{
    public function test_remove_html_tags_and_content()
    {
        $str = "<P><em>test</em></P>";
        $this->assertEquals('<P></P>', Helper::removeHtmlTagsAndContent($str, ['em']));
    }

    public function test_count_words()
    {
        $str = 'Waves crashing on the shore';
        $this->assertEquals(5, Helper::countWords($str));

        $str = '岸に打ち寄せる波';
        $this->assertEquals(4, Helper::countWords($str));

        $str = 'Từng con sóng đang xô bờ';
        $this->assertEquals(6, Helper::countWords($str));
    }
}