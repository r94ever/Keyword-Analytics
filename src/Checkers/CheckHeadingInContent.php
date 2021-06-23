<?php

namespace Qmas\KeywordAnalytics\Checkers;

use PHPHtmlParser\Dom\Node\Collection;
use Qmas\KeywordAnalytics\Abstracts\Checker;
use Qmas\KeywordAnalytics\CheckingMessage;

class CheckHeadingInContent extends Checker
{
    private $min;

    protected $dom;

    /** @var Collection $headings */
    protected $headings;

    /** @var int $headingCount */
    protected $headingCount = 0;

    /**
     * CheckHeadingInContent constructor.
     * @param $headings
     */
    public function __construct($headings)
    {
        parent::__construct();

        $this->min = config('keyword-analytics.variables.heading_in_content.min');

        $this->headings = $headings;
        $this->headingCount = $this->headings->count();
    }

    public function check(): Checker
    {
        if ($this->headingCount === 0) {
            $this->result->push($this->msgIfEmpty());
        }
        elseif ($this->headingCount < $this->min) {
            $this->result->push($this->msgIfTooLow());
        }
        else {
            $this->result->push($this->msgIfOk());
        }

        return $this;
    }

    protected function msgIfEmpty(): array
    {
        return (new CheckingMessage(
            CheckingMessage::IGNORED_TYPE,
            CheckingMessage::HTML_FIELD,
            CheckingMessage::IGNORE_MSG_ID,
            __('The content should contain heading tags.'),
            CheckingMessage::HEADING_VALIDATOR,
            ["headingCount" => $this->headingCount, "min" => $this->min,]
        ))->build();
    }

    protected function msgIfTooLow(): array
    {
        return (new CheckingMessage(
            CheckingMessage::ERROR_TYPE,
            CheckingMessage::HTML_FIELD,
            CheckingMessage::IGNORE_MSG_ID,
            __('The content should contain at least :num heading tags.', ['num' => $this->min]),
            CheckingMessage::HEADING_VALIDATOR,
            ["headingCount" => $this->headingCount, "min" => $this->min,]
        ))->build();
    }

    protected function msgIfOk(): array
    {
        return (new CheckingMessage(
            CheckingMessage::SUCCESS_TYPE,
            CheckingMessage::HTML_FIELD,
            CheckingMessage::SUCCESS_MSG_ID,
            __('The content is containing one or more heading tags.'),
            CheckingMessage::HEADING_VALIDATOR,
            ["headingCount" => $this->headingCount, "min" => $this->min,]
        ))->build();
    }
}
