<?php

namespace Qmas\KeywordAnalytics\Checkers;

use Qmas\KeywordAnalytics\Abstracts\Checker;
use Qmas\KeywordAnalytics\CheckingMessage;
use Qmas\KeywordAnalytics\Helper;

class CheckKeywordInUrl extends Checker
{
    private $min;
    
    private $max;

    protected $url;

    protected $keyword;

    protected $keywordCount;

    public function __construct($url, $keyword)
    {
        parent::__construct();

        $this->min = config('keyword-analytics.variables.keyword_in_url.min');
        $this->max = config('keyword-analytics.variables.keyword_in_url.max');

        $this->url = Helper::unicodeToAscii($url);
        $this->keyword = Helper::unicodeToAscii($keyword);

        $this->keywordCount = $this->findKeywordInUrl();
    }

    protected function findKeywordInUrl()
    {
        $string = $this->extractAlphaNumOnlyFromUrl();

        return preg_match_all("/$this->keyword/i", $string);
    }

    protected function extractAlphaNumOnlyFromUrl()
    {
        return preg_replace('/[^a-zA-Z0-9]/', ' ', $this->url);
    }

    public function check(): Checker
    {
        if (! $this->url) {
            $this->result->push($this->msgIfUrlUnavailable());
        }
        elseif ($this->keywordCount === 0) {
            $this->result->push($this->msgIfNotFound());
        }
        elseif ($this->keywordCount < $this->min) {
            $this->result->push($this->msgIfTooLow());
        }
        elseif ($this->keywordCount > $this->max) {
            $this->result->push($this->msgIfTooOften());
        }
        else {
            $this->result->push($this->msgIfOk());
        }

        return $this;
    }

    protected function msgIfUrlUnavailable(): array
    {
        return (new CheckingMessage(
            CheckingMessage::IGNORED_TYPE,
            CheckingMessage::URL_FIELD,
            CheckingMessage::IGNORE_MSG_ID,
            '',
            CheckingMessage::KEYWORD_COUNT_VALIDATOR,
            ["min" => $this->min, "max" => $this->max,]
        ))->build();
    }

    protected function msgIfNotFound(): array
    {
        return (new CheckingMessage(
            CheckingMessage::ERROR_TYPE,
            CheckingMessage::URL_FIELD,
            CheckingMessage::KEYWORD_NOT_FOUND_MSG_ID,
            __('The url does not contain the keyword.'),
            CheckingMessage::KEYWORD_COUNT_VALIDATOR,
            ["min" => $this->min, "max" => $this->max, "keywordCount" => $this->keywordCount]
        ))->build();
    }

    protected function msgIfTooLow(): array
    {
        return (new CheckingMessage(
            CheckingMessage::ERROR_TYPE,
            CheckingMessage::URL_FIELD,
            CheckingMessage::KEYWORD_TOO_LOW_MSG_ID,
            __('The url should only contain the keyword once or twice.'),
            CheckingMessage::KEYWORD_COUNT_VALIDATOR,
            ["min" => $this->min, "max" => $this->max, "keywordCount" => $this->keywordCount]
        ))->build();
    }

    protected function msgIfTooOften(): array
    {
        return (new CheckingMessage(
            CheckingMessage::ERROR_TYPE,
            CheckingMessage::URL_FIELD,
            CheckingMessage::KEYWORD_TOO_OFTEN_MSG_ID,
            __('The url should only contain the keyword once or twice.'),
            CheckingMessage::KEYWORD_COUNT_VALIDATOR,
            ["min" => $this->min, "max" => $this->max, "keywordCount" => $this->keywordCount]
        ))->build();
    }

    protected function msgIfOk(): array
    {
        return (new CheckingMessage(
            CheckingMessage::SUCCESS_TYPE,
            CheckingMessage::URL_FIELD,
            CheckingMessage::SUCCESS_MSG_ID,
            __('The url contains the keyword once or twice.'),
            CheckingMessage::KEYWORD_COUNT_VALIDATOR,
            ["min" => $this->min, "max" => $this->max, "keywordCount" => $this->keywordCount]
        ))->build();
    }
}
