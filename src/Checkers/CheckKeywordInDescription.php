<?php

namespace Qmas\KeywordAnalytics\Checkers;

use Qmas\KeywordAnalytics\CheckingMessage;
use Qmas\KeywordAnalytics\Enums\CheckResultType;
use Qmas\KeywordAnalytics\Enums\Field;
use Qmas\KeywordAnalytics\Enums\MessageId;
use Qmas\KeywordAnalytics\Enums\Validator;

class CheckKeywordInDescription extends CheckDescriptionLength
{
    private int $min;

    private int $max;

    protected string $keyword;

    protected int $keywordCount = 0;

    protected CheckingMessage $message;

    public function __construct($keyword, $description)
    {
        parent::__construct($description);

        $this->min = (int) config('keyword-analytics.variables.keyword_in_description.min');
        $this->max = (int) config('keyword-analytics.variables.keyword_in_description.max');

        $this->keyword = $keyword;

        $this->message = CheckingMessage::make()
            ->setValidatorName(Validator::KEYWORD_COUNT)
            ->setField(Field::DESCRIPTION);
    }

    protected function countKeyword(): void
    {
        $this->keywordCount = preg_match_all('/('.$this->keyword.')/im', $this->description);
    }

    public function check(): Checker
    {
        if ($this->descriptionCharactersCount === 0) {
            $this->result->push($this->msgIfDescriptionEmpty());
            return $this;
        }

        $this->countKeyword();

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

    protected function msgIfDescriptionEmpty(): array
    {
        return $this->message
            ->setType(CheckResultType::IGNORED)
            ->setMsgId(MessageId::IGNORE)
            ->setMsg(__('The meta description is empty.'))
            ->setData(["min" => $this->min, "max" => $this->max])
            ->build();
    }

    protected function msgIfKeywordNotFound(): array
    {
        return $this->message
            ->setType(CheckResultType::ERROR)
            ->setMsgId(MessageId::KEYWORD_NOT_FOUND)
            ->setMsg(__('The meta description does not contain the keyword.'))
            ->setData(["min" => $this->min, "max" => $this->max, "keywordCount" => 0])
            ->build();
    }

    protected function msgIfKeywordTooLow(): array
    {
        return $this->message
            ->setType(CheckResultType::ERROR)
            ->setMsgId(MessageId::KEYWORD_TOO_LOW)
            ->setMsg(__('The meta description contains the keyword less than :min times.', ['min' => $this->min]))
            ->setData(["min" => $this->min, "max" => $this->max, "keywordCount" => $this->keywordCount])
            ->build();
    }

    protected function msgIfKeywordTooOften(): array
    {
        return $this->message
            ->setType(CheckResultType::ERROR)
            ->setMsgId(MessageId::KEYWORD_TOO_OFTEN)
            ->setMsg(__('The meta description contains the keyword more than :max times.', ['max' => $this->max]))
            ->setData(["min" => $this->min, "max" => $this->max, "keywordCount" => $this->keywordCount])
            ->build();
    }

    protected function msgIfEnough(): array
    {
        return $this->message
            ->setType(CheckResultType::SUCCESS)
            ->setMsgId(MessageId::SUCCESS)
            ->setMsg(__('The keyword was found in the meta description :count times.', [
                'count' => $this->keywordCount
            ]))
            ->setData(["min" => $this->min, "max" => $this->max, "keywordCount" => $this->keywordCount])
            ->build();
    }
}
