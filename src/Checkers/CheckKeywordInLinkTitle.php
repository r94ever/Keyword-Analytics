<?php

namespace Qmas\KeywordAnalytics\Checkers;

use PHPHtmlParser\Dom\Node\Collection;
use PHPHtmlParser\Dom\Node\HtmlNode;
use Qmas\KeywordAnalytics\Abstracts\Checker;
use Qmas\KeywordAnalytics\CheckingMessage;
use Qmas\KeywordAnalytics\Helper;

class CheckKeywordInLinkTitle extends Checker
{
    private $min;

    /** @var Collection */
    protected $links;

    protected $keyword;

    protected $keywordCount = 0;

    public function __construct($links, $keyword)
    {
        parent::__construct();

        $this->min = config('keyword-analytics.variables.keyword_in_link_title.min');

        $this->keyword = $keyword;
        $this->links = $links;
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
        $this->links->each(function ($link) {
            /** @var HtmlNode $link */
            $title = Helper::unicodeToAscii($link->getAttribute('title'));

            if (Helper::strContains($title, $this->keyword)) {
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
            __('Great. The TITLE attribute in your A tags containing the keyword.'),
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
            __('The keyword should appear in the TITLE attribute of at least :min links', ['min' => $this->min]),
            CheckingMessage::KEYWORD_COUNT_VALIDATOR,
            ['min' => $this->min, 'keywordCount' => $this->keywordCount]
        ))->build();
    }

    protected function msgIfNotContain(): array
    {
        return (new CheckingMessage(
            CheckingMessage::WARNING_TYPE,
            CheckingMessage::HTML_FIELD,
            CheckingMessage::KEYWORD_NOT_FOUND_MSG_ID,
            __('The TITLE attribute in your link tags should contain the keyword.'),
            CheckingMessage::KEYWORD_COUNT_VALIDATOR,
            ['min' => $this->min, 'keywordCount' => 0]
        ))->build();
    }
}