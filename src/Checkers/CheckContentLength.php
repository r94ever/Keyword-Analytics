<?php

namespace Qmas\KeywordAnalytics\Checkers;

use Qmas\KeywordAnalytics\Abstracts\Checker;
use Qmas\KeywordAnalytics\CheckingMessage;
use Qmas\KeywordAnalytics\Helper;

class CheckContentLength extends Checker
{
    private $min;

    protected $contentWithoutHtml;

    protected $htmlWordsCount = 0;

    public function __construct($contentWithoutHtml)
    {
        parent::__construct();

        $this->min = config('keyword-analytics.variables.content_length.min');

        $this->contentWithoutHtml = $contentWithoutHtml;
        $this->htmlWordsCount = Helper::countWords($this->contentWithoutHtml);
    }

    public function check(): Checker
    {
        if (! $this->contentWithoutHtml) {
            $this->result->push($this->msgIfEmpty());
        }
        elseif ($this->htmlWordsCount < $this->min) {
            $this->result->push($this->msgIfTooShort());
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
            '',
            CheckingMessage::WORD_COUNT_VALIDATOR,
            ["min" => $this->min]
        ))->build();
    }

    protected function msgIfTooShort(): array
    {
        return (new CheckingMessage(
            CheckingMessage::WARNING_TYPE,
            CheckingMessage::HTML_FIELD,
            CheckingMessage::TOO_SHORT_MSG_ID,
            __('The text should contain more then :min words to be recognized as relevant.', ['min' => $this->min]),
            CheckingMessage::WORD_COUNT_VALIDATOR,
            ["wordCount" => $this->htmlWordsCount, "min" => $this->min,]
        ))->build();
    }

    protected function msgIfOk(): array
    {
        return (new CheckingMessage(
            CheckingMessage::SUCCESS_TYPE,
            CheckingMessage::DESCRIPTION_FIELD,
            CheckingMessage::SUCCESS_MSG_ID,
            __('Great. The text contains more than :min words.', ['min' => $this->min]),
            CheckingMessage::LENGTH_VALIDATOR,
            ["wordCount" => $this->htmlWordsCount, "min" => $this->min]
        ))->build();
    }
}
