<?php

namespace Qmas\KeywordAnalytics;

class Helper
{
    public static function removeHtmlTagsAndContent(string $html, array $tags)
    {
        foreach ($tags as $tag) {
            $html = preg_replace(
                '/<'.$tag.'(?:\s+[a-z]+\s*=\s*(?:"[^"]*"|\'[^\']*\'|[^\s>]+))*\s*>([\S\s]*)<\/'.$tag.'>/mi',
                '',
                $html
            );
        }

        return $html;
    }

    /**
     * Same as str_word_count() but support Unicode characters
     *
     * @param string $string
     * @return int
     */
    public static function countWords(string $string): int
    {
        return str($string)->transliterate()->wordCount();
    }
}