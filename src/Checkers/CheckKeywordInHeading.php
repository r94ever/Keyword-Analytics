<?php

namespace Qmas\KeywordAnalytics\Checkers;

use Qmas\KeywordAnalytics\Abstracts\Checker;
use Qmas\KeywordAnalytics\CheckingMessage;
use Qmas\KeywordAnalytics\Helper;

class CheckKeywordInHeading extends Checker
{
    private $min;

    /** @var string $keyword */
    protected $keyword;

    /** @var \PHPHtmlParser\Dom\Collection */
    protected $headings;

    protected $headingsContainKeyword = 0;

    public function __construct($keyword, $headings)
    {
        parent::__construct();

        $this->min = config('keyword-analytics.variables.keyword_in_heading.min');

        $this->keyword = $keyword;
        $this->headings = $headings;
    }

    public function check(): Checker
    {
        $this->headings->each(function($heading) {
            /** @var \PHPHtmlParser\Dom\HtmlNode $heading */
            $innerHeading = Helper::unicodeToAscii($heading->innerHtml());

            if (Helper::strContains($innerHeading, $this->keyword)) {
                $this->headingsContainKeyword += 1;
            }
        });

        if ($this->headingsContainKeyword > 0) {
            $this->result->push($this->msgIfContained());
        }
        else {
            $this->result->push($this->msgIfEmpty());
        }

        return $this;
    }

    protected function msgIfNoHeading(): array
    {
        return (new CheckingMessage(
            CheckingMessage::IGNORED_TYPE,
            CheckingMessage::HTML_FIELD,
            CheckingMessage::IGNORE_MSG_ID,
            '',
            CheckingMessage::HEADING_VALIDATOR,
            ['min' => $this->min, 'keywordCount' => 0]
        ))->build();
    }

    protected function msgIfEmpty(): array
    {
        return (new CheckingMessage(
            CheckingMessage::ERROR_TYPE,
            CheckingMessage::HTML_FIELD,
            CheckingMessage::KEYWORD_NOT_FOUND_MSG_ID,
            __('No heading containing keyword was detected.'),
            CheckingMessage::HEADING_VALIDATOR,
            ['min' => $this->min, 'keywordCount' => 0]
        ))->build();
    }

    protected function msgIfContained(): array
    {
        return (new CheckingMessage(
            CheckingMessage::SUCCESS_TYPE,
            CheckingMessage::HTML_FIELD,
            CheckingMessage::SUCCESS_MSG_ID,
            __('Your headings should contain the keyword.'),
            CheckingMessage::HEADING_VALIDATOR,
            ['min' => $this->min, 'headingCount' => $this->headingsContainKeyword]
        ))->build();
    }
}
