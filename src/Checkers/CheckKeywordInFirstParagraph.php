<?php

namespace Qmas\KeywordAnalytics\Checkers;

use Qmas\KeywordAnalytics\Abstracts\Checker;
use Qmas\KeywordAnalytics\CheckingMessage;
use Qmas\KeywordAnalytics\Helper;

class CheckKeywordInFirstParagraph extends Checker
{
    private $min;
    
    private $max;

    protected $keyword;

    protected $html;

    protected $firstPara;

    protected $keywordCount;

    public function __construct($keyword, $html)
    {
        parent::__construct();

        $this->min = config('keyword-analytics.variables.keyword_in_first_paragraph.min');
        $this->max = config('keyword-analytics.variables.keyword_in_first_paragraph.max');

        $this->keyword = $keyword;
        $this->html = $html;
        $this->firstPara = $this->getFirstParagraph();
    }

    protected function getFirstParagraph(): string
    {
        $matches = [];
        $search = preg_match('/<([a-zA-Z]+)>(.*?)<\/\1>/is', $this->html, $matches);

        return $search === 1 ? Helper::unicodeToAscii($matches[2]) : '';
    }

    public function check(): Checker
    {
        if (! $this->firstPara) {
            $this->result->push($this->msgIfFirstParaIsEmpty());
            return $this;
        }

        $this->keywordCount = preg_match_all('/('.$this->keyword.')/im', $this->firstPara);

        if ($this->keywordCount === 0) {
            $this->result->push($this->msgIfKeywordNotFound());
        }
        elseif ($this->keywordCount < $this->min) {
            $this->result->push($this->msgIfKeywordTooLow());
        }
        elseif ($this->keywordCount > $this->max) {
            $this->result->push($this->msgIfKeywordTooOften());
        }
        else {
            $this->result->push($this->msgIfEnough());
        }

        return $this;
    }

    protected function msgIfFirstParaIsEmpty(): array
    {
        return (new CheckingMessage(
            CheckingMessage::IGNORED_TYPE,
            CheckingMessage::HTML_FIELD,
            CheckingMessage::IGNORE_MSG_ID,
            '',
            CheckingMessage::KEYWORD_COUNT_VALIDATOR,
            ["min" => $this->min, "max" => $this->max]
        ))->build();
    }

    protected function msgIfKeywordNotFound(): array
    {
        return (new CheckingMessage(
            CheckingMessage::WARNING_TYPE,
            CheckingMessage::HTML_FIELD,
            CheckingMessage::KEYWORD_NOT_FOUND_MSG_ID,
            __('The first paragraph of content does not contain the keyword.'),
            CheckingMessage::KEYWORD_COUNT_VALIDATOR,
            ["min" => $this->min, "max" => $this->max, "keywordCount" => 0,]
        ))->build();
    }

    protected function msgIfKeywordTooLow(): array
    {
        return (new CheckingMessage(
            CheckingMessage::ERROR_TYPE,
            CheckingMessage::HTML_FIELD,
            CheckingMessage::KEYWORD_TOO_LOW_MSG_ID,
            __('The first paragraph of content contains the keyword less than :min times.', ['min' => $this->min]),
            CheckingMessage::KEYWORD_COUNT_VALIDATOR,
            ["min" => $this->min, "max" => $this->max, "keywordCount" => $this->keywordCount,]
        ))->build();
    }

    protected function msgIfKeywordTooOften(): array
    {
        return (new CheckingMessage(
            CheckingMessage::WARNING_TYPE,
            CheckingMessage::HTML_FIELD,
            CheckingMessage::KEYWORD_TOO_OFTEN_MSG_ID,
            __('The first paragraph of content contains the keyword more than :max times.', ['max' => $this->max]),
            CheckingMessage::KEYWORD_COUNT_VALIDATOR,
            ["min" => $this->min, "max" => $this->max, "keywordCount" => $this->keywordCount,]
        ))->build();
    }

    protected function msgIfEnough(): array
    {
        return (new CheckingMessage(
            CheckingMessage::SUCCESS_TYPE,
            CheckingMessage::HTML_FIELD,
            CheckingMessage::SUCCESS_MSG_ID,
            __('The first paragraph of content should contain the keyword.'),
            CheckingMessage::KEYWORD_COUNT_VALIDATOR,
            ["min" => $this->min, "max" => $this->max, "keywordCount" => $this->keywordCount,]
        ))->build();
    }
}
