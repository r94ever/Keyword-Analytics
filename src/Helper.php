<?php

namespace Qmas\KeywordAnalytics;

class Helper
{
    /**
     * Strip all HTML tags in the given string
     *
     * @param string $html
     * @return string
     */
    public static function stripHtmlTags(string $html, bool $removeContent = false): string
    {
        return strip_tags($html);
/*        return preg_replace('/<.*?>/', '', $html);*/
    }

    /**
     * Convert all unicode words to ASCII
     *
     * @param string $string
     * @param bool $lowercase
     * @return string
     */
    public static function unicodeToAscii(string $string = null, bool $lowercase = true): string
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
        return count(preg_split('~[^\p{L}\p{N}\']+~u', $string));
    }

    /**
     * Determine if a given string contains a given substring.
     *
     * @param string $haystack
     * @param  string|string[]  $needles
     * @return bool
     */
    public static function strContains(string $haystack, $needles): bool
    {
        if (class_exists('\Str')) {
            return \Str::contains($haystack, $needles);
        }

        foreach ((array) $needles as $needle) {
            if ($needle !== '' && mb_strpos($haystack, $needle) !== false) {
                return true;
            }
        }

        return false;

    }
}