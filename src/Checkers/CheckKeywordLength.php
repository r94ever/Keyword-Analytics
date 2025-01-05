<?php

namespace Qmas\KeywordAnalytics\Checkers;

use Qmas\KeywordAnalytics\CheckingMessage;
use Qmas\KeywordAnalytics\Enums\CheckResultType;
use Qmas\KeywordAnalytics\Enums\Field;
use Qmas\KeywordAnalytics\Enums\MessageId;
use Qmas\KeywordAnalytics\Enums\Validator;
use Qmas\KeywordAnalytics\Helper;

class CheckKeywordLength extends Checker
{
    private int $min;

    private int $max;

    protected int $keywordWordsCount = 0;

    protected CheckingMessage $message;

    /**
     * CheckKeywordLength constructor.
     * @param $keyword
     */
    public function __construct($keyword)
    {
        parent::__construct();
        
        $this->min = (int) config('keyword-analytics.variables.keyword_length.min');
        $this->max = (int) config('keyword-analytics.variables.keyword_length.max');

        $this->keywordWordsCount = Helper::countWords($keyword);
        $this->result = collect();

        $this->message = CheckingMessage::make()
            ->setValidatorName(Validator::LENGTH)
            ->setField(Field::KEYWORD);
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
        return $this->message
            ->setType(CheckResultType::SUCCESS)
            ->setMsgId(MessageId::SUCCESS)
            ->setMsg(__("The keyword / keyphrase should be more than :min and less than :max words.", [
                'min' => $this->min,
                'max' => $this->max
            ]))
            ->setData(["wordCount" => $this->keywordWordsCount, "min" => $this->min, "max" => $this->max])
            ->build();
    }

    protected function msgIfTooShort(): array
    {
        return $this->message
            ->setType(CheckResultType::ERROR)
            ->setMsgId(MessageId::TOO_SHORT)
            ->setMsg(__("The keyword / keyphrase should be more than :min and less than :max words.", [
                'min' => $this->min,
                'max' => $this->max
            ]))
            ->setData(["wordCount" => $this->keywordWordsCount, "min" => $this->min, "max" => $this->max])
            ->build();
    }

    protected function msgIfTooLong(): array
    {
        return $this->message
            ->setType(CheckResultType::ERROR)
            ->setMsgId(MessageId::TOO_LONG)
            ->setMsg(__("The keyword / keyphrase should be more than :min and less than :max words.", [
                'min' => $this->min,
                'max' => $this->max
            ]))
            ->setData(["wordCount" => $this->keywordWordsCount, "min" => $this->min, "max" => $this->max])
            ->build();
    }
}
