<?php

namespace Qmas\KeywordAnalytics\Checkers;

use PHPHtmlParser\Dom\Node\HtmlNode;
use Qmas\KeywordAnalytics\Abstracts\Checker;
use Qmas\KeywordAnalytics\CheckingMessage;
use Qmas\KeywordAnalytics\Helper;

class CheckKeywordInImgAlt extends CheckImageInContent
{
    private $min;

    protected $keyword;

    protected $keywordCount = 0;

    public function __construct($images, $keyword)
    {
        parent::__construct($images);

        $this->min = config('keyword-analytics.variables.keyword_in_alt_image.min');

        $this->keyword = $keyword;
    }

    public function check(): Checker
    {
        $this->countKeyword();

        if ($this->keywordCount === 0) {
            $this->result->push($this->msgIfNotContain());
        }
        elseif ($this->keywordCount < $this->min) {
            $this->result->push($this->msgIfTooLow());
        }
        else {
            $this->result->push($this->msgIfContain());
        }

        return $this;
    }

    protected function countKeyword()
    {
        $this->images->each(function ($image) {
            /** @var HtmlNode $image */
            $alt = Helper::unicodeToAscii($image->getAttribute('alt'));

            if (Helper::strContains($alt, $this->keyword)) {
                $this->keywordCount += 1;
            }
        });
    }

    protected function msgIfContain(): array
    {
        return (new CheckingMessage(
            CheckingMessage::SUCCESS_TYPE,
            CheckingMessage::HTML_FIELD,
            CheckingMessage::SUCCESS_MSG_ID,
            __('Great. The ALT attribute in your IMG tags containing the keyword.'),
            CheckingMessage::KEYWORD_COUNT_VALIDATOR,
            ['min' => $this->min, 'keywordCount' => $this->keywordCount]
        ))->build();
    }

    protected function msgIfTooLow(): array
    {
        return (new CheckingMessage(
            CheckingMessage::WARNING_TYPE,
            CheckingMessage::HTML_FIELD,
            CheckingMessage::KEYWORD_TOO_LOW_MSG_ID,
            __('The keyword should appear in the ALT attribute of at least :min IMG tags', ['min' => $this->min]),
            CheckingMessage::KEYWORD_COUNT_VALIDATOR,
            ['min' => $this->min, 'keywordCount' => $this->keywordCount]
        ))->build();
    }

    protected function msgIfNotContain(): array
    {
        return (new CheckingMessage(
            CheckingMessage::ERROR_TYPE,
            CheckingMessage::HTML_FIELD,
            CheckingMessage::KEYWORD_NOT_FOUND_MSG_ID,
            __('The ALT attribute in your IMG tags should contain the keyword.'),
            CheckingMessage::KEYWORD_COUNT_VALIDATOR,
            ['min' => $this->min, 'keywordCount' => 0]
        ))->build();
    }
}
