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
     * Convert all unicode words to ASCII
     *
     * @param ?string $string
     * @param bool $lowercase
     * @return string
     */
    public static function unicodeToAscii(?string $string = '', bool $lowercase = true): string
    {
        $transliterator = "Any-Latin; NFD; [:Nonspacing Mark:] Remove; NFC; [:Punctuation:] Remove;";

        if ($lowercase) {
            $transliterator .= " Lower();";
        }

        return transliterator_transliterate($transliterator, $string);
    }

    /**
     * Same as str_word_count() but support Unicode characters
     *
     * @param string $string
     * @return int
     */
    public static function countWords(string $string): int
    {
        return count(preg_split('/\W+/uim', $string, -1, PREG_SPLIT_NO_EMPTY));
    }
}