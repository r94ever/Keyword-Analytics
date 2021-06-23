<?php

namespace Qmas\KeywordAnalytics\Checkers;

use Qmas\KeywordAnalytics\Abstracts\Checker;
use Qmas\KeywordAnalytics\CheckingMessage;

class CheckKeywordInTitle extends CheckTitleLength
{
    private $min;

    private $max;

    protected $keyword;

    protected $keywordCount = 0;

    public function __construct($keyword, $description)
    {
        parent::__construct($description);

        $this->min = config('keyword-analytics.variables.keyword_in_title.min');
        $this->max = config('keyword-analytics.variables.keyword_in_title.max');

        $this->keyword = $keyword;
    }

    public function check(): Checker
    {
        if ($this->titleCharactersCount === 0) {
            $this->result->push($this->msgIfTitleEmpty());
            return $this;
        }

        $this->keywordCount = preg_match_all('/('.$this->keyword.')/i', $this->title);

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

    protected function msgIfTitleEmpty(): array
    {
        return (new CheckingMessage(
            CheckingMessage::IGNORED_TYPE,
            CheckingMessage::TITLE_FIELD,
            CheckingMessage::IGNORE_MSG_ID,
            __('The title is empty.'),
            CheckingMessage::KEYWORD_COUNT_VALIDATOR,
            ["min" => $this->min, "max" => $this->max]
        ))->build();
    }

    protected function msgIfKeywordNotFound(): array
    {
        return (new CheckingMessage(
            CheckingMessage::ERROR_TYPE,
            CheckingMessage::TITLE_FIELD,
            CheckingMessage::KEYWORD_NOT_FOUND_MSG_ID,
            __('The page title does not contain the keyword.'),
            CheckingMessage::KEYWORD_COUNT_VALIDATOR,
            ["min" => $this->min, "max" => $this->max, "keywordCount" => 0]
        ))->build();
    }

    protected function msgIfKeywordTooLow(): array
    {
        return (new CheckingMessage(
            CheckingMessage::ERROR_TYPE,
            CheckingMessage::TITLE_FIELD,
            CheckingMessage::KEYWORD_TOO_LOW_MSG_ID,
            __('The page title should only contain the keyword once or twice.'),
            CheckingMessage::KEYWORD_COUNT_VALIDATOR,
            ["min" => $this->min, "max" => $this->max, "keywordCount" => $this->keywordCount]
        ))->build();
    }

    protected function msgIfKeywordTooOften(): array
    {
        return (new CheckingMessage(
            CheckingMessage::ERROR_TYPE,
            CheckingMessage::TITLE_FIELD,
            CheckingMessage::KEYWORD_TOO_OFTEN_MSG_ID,
            __('The page title should only contain the keyword once or twice.'),
            CheckingMessage::KEYWORD_COUNT_VALIDATOR,
            ["min" => $this->min, "max" => $this->max, "keywordCount" => $this->keywordCount]
        ))->build();
    }

    protected function msgIfEnough(): array
    {
        return (new CheckingMessage(
            CheckingMessage::SUCCESS_TYPE,
            CheckingMessage::TITLE_FIELD,
            CheckingMessage::SUCCESS_MSG_ID,
            __('The page title contains the keyword once or twice.'),
            CheckingMessage::KEYWORD_COUNT_VALIDATOR,
            ["min" => $this->min, "max" => $this->max, "keywordCount" => $this->keywordCount]
        ))->build();
    }
}
