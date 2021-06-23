<?php

namespace Qmas\KeywordAnalytics\Checkers;

use Qmas\KeywordAnalytics\Abstracts\Checker;
use Qmas\KeywordAnalytics\CheckingMessage;
use Qmas\KeywordAnalytics\Helper;

class CheckKeywordLength extends Checker
{
    private $min;

    private $max;

    protected $keywordWordsCount = 0;

    /**
     * CheckKeywordLength constructor.
     * @param $keyword
     */
    public function __construct($keyword)
    {
        parent::__construct();
        
        $this->min = config('keyword-analytics.variables.keyword_length.min');
        $this->max = config('keyword-analytics.variables.keyword_length.max');

        $this->keywordWordsCount = Helper::countWords($keyword);
        $this->result = collect();
    }

    /**
     * @return $this|Checker
     */
    public function check(): Checker
    {
        if ($this->keywordWordsCount < $this->min) {
            $this->result->push($this->msgIfTooShort());
        }
        elseif ($this->keywordWordsCount > $this->max) {
            $this->result->push($this->msgIfTooLong());
        }
        else {
            $this->result->push($this->msgIfOk());
        }

        return $this;
    }

    protected function msgIfOk(): array
    {
        return (new CheckingMessage(
            CheckingMessage::SUCCESS_TYPE,
            CheckingMessage::KEYWORD_FIELD,
            CheckingMessage::SUCCESS_MSG_ID,
            __("The keyword / keyphrase should be more than :min and less than :max words.", [
                'min' => $this->min,
                'max' => $this->max
            ]),
            CheckingMessage::LENGTH_VALIDATOR,
            [
                "wordCount" => $this->keywordWordsCount,
                "min" => $this->min,
                "max" => $this->max
            ]
        ))->build();
    }

    protected function msgIfTooShort(): array
    {
        return (new CheckingMessage(
            CheckingMessage::ERROR_TYPE,
            CheckingMessage::KEYWORD_FIELD,
            CheckingMessage::TOO_SHORT_MSG_ID,
            __("The keyword / keyphrase should be more than :min and less than :max words.", [
                'min' => $this->min,
                'max' => $this->max
            ]),
            CheckingMessage::LENGTH_VALIDATOR,
            [
                "wordCount" => $this->keywordWordsCount,
                "min" => $this->min,
                "max" => $this->max
            ]
        ))->build();
    }

    protected function msgIfTooLong(): array
    {
        return (new CheckingMessage(
            CheckingMessage::ERROR_TYPE,
            CheckingMessage::KEYWORD_FIELD,
            CheckingMessage::TOO_LONG_MSG_ID,
            __("The keyword / keyphrase should be more than :min and less than :max words.", [
                'min' => $this->min,
                'max' => $this->max
            ]),
            CheckingMessage::LENGTH_VALIDATOR,
            [
                "wordCount" => $this->keywordWordsCount,
                "min" => $this->min,
                "max" => $this->max
            ]
        ))->build();
    }
}
