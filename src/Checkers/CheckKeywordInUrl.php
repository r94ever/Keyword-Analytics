<?php

namespace Qmas\KeywordAnalytics\Checkers;

use Qmas\KeywordAnalytics\CheckingMessage;
use Qmas\KeywordAnalytics\Enums\CheckResultType;
use Qmas\KeywordAnalytics\Enums\Field;
use Qmas\KeywordAnalytics\Enums\MessageId;
use Qmas\KeywordAnalytics\Enums\Validator;
use Qmas\KeywordAnalytics\Helper;

class CheckKeywordInUrl extends Checker
{
    private int $min;
    
    private int $max;

    protected string $url;

    protected string $keyword;

    protected int $keywordCount;

    protected CheckingMessage $message;

    public function __construct($url, $keyword)
    {
        parent::__construct();

        $this->min = (int) config('keyword-analytics.variables.keyword_in_url.min');
        $this->max = (int) config('keyword-analytics.variables.keyword_in_url.max');

        $this->url = str($url)->transliterate();
        $this->keyword = str($keyword)->transliterate();
        $this->keywordCount = $this->findKeywordInUrl();

        $this->message = CheckingMessage::make()
            ->setValidatorName(Validator::KEYWORD_COUNT)
            ->setField(Field::URL);
    }

    protected function findKeywordInUrl(): false|int
    {
        $string = $this->extractAlphaNumOnlyFromUrl();

        return preg_match_all("/$this->keyword/i", $string);
    }

    protected function extractAlphaNumOnlyFromUrl(): array|string|null
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
        return $this->message
            ->setType(CheckResultType::IGNORED)
            ->setMsgId(MessageId::IGNORE)
            ->setData(["min" => $this->min, "max" => $this->max])
            ->build();
    }

    protected function msgIfNotFound(): array
    {
        return $this->message
            ->setType(CheckResultType::ERROR)
            ->setMsgId(MessageId::KEYWORD_NOT_FOUND)
            ->setMsg(__('The url does not contain the keyword.'))
            ->setData(["min" => $this->min, "max" => $this->max, "keywordCount" => $this->keywordCount])
            ->build();
    }

    protected function msgIfTooLow(): array
    {
        return $this->message
            ->setType(CheckResultType::ERROR)
            ->setMsgId(MessageId::KEYWORD_TOO_LOW)
            ->setMsg(__('The keyword should appear in the URL at least :min times.', ['min' => $this->min]))
            ->setData(["min" => $this->min, "max" => $this->max, "keywordCount" => $this->keywordCount])
            ->build();
    }

    protected function msgIfTooOften(): array
    {
        return $this->message
            ->setType(CheckResultType::ERROR)
            ->setMsgId(MessageId::KEYWORD_TOO_OFTEN)
            ->setMsg(__('Keywords should not appear in the URL more than :max times.', ['max' => $this->max]))
            ->setData(["min" => $this->min, "max" => $this->max, "keywordCount" => $this->keywordCount])
            ->build();
    }

    protected function msgIfOk(): array
    {
        return $this->message
            ->setType(CheckResultType::SUCCESS)
            ->setMsgId(MessageId::SUCCESS)
            ->setMsg(__('Keywords appear in the URL at a reasonable density.'))
            ->setData(["min" => $this->min, "max" => $this->max, "keywordCount" => $this->keywordCount])
            ->build();
    }
}
