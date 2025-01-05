<?php

namespace Qmas\KeywordAnalytics\Checkers;

use Qmas\KeywordAnalytics\CheckingMessage;
use Qmas\KeywordAnalytics\Enums\CheckResultType;
use Qmas\KeywordAnalytics\Enums\Field;
use Qmas\KeywordAnalytics\Enums\MessageId;
use Qmas\KeywordAnalytics\Enums\Validator;

class CheckKeywordInTitle extends CheckTitleLength
{
    private int $min;

    private int $max;

    protected string $keyword;

    protected int $keywordCount = 0;

    protected CheckingMessage $message;

    public function __construct($keyword, $description)
    {
        parent::__construct($description);

        $this->min = config('keyword-analytics.variables.keyword_in_title.min');
        $this->max = config('keyword-analytics.variables.keyword_in_title.max');

        $this->keyword = $keyword;

        $this->message = CheckingMessage::make()
            ->setValidatorName(Validator::KEYWORD_COUNT)
            ->setField(Field::TITLE);
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
        return $this->message
            ->setType(CheckResultType::IGNORED)
            ->setMsgId(MessageId::IGNORE)
            ->setMsg(__('The page title is empty.'))
            ->setData(["min" => $this->min, "max" => $this->max])
            ->build();
    }

    protected function msgIfKeywordNotFound(): array
    {
        return $this->message
            ->setType(CheckResultType::ERROR)
            ->setMsgId(MessageId::KEYWORD_NOT_FOUND)
            ->setMsg(__('The page title does not contain the keyword.'))
            ->setData(["min" => $this->min, "max" => $this->max, "keywordCount" => 0])
            ->build();
    }

    protected function msgIfKeywordTooLow(): array
    {
        return $this->message
            ->setType(CheckResultType::WARNING)
            ->setMsgId(MessageId::KEYWORD_TOO_LOW)
            ->setMsg(__('The keyword should appear in the title at least :min times.', ['min' => $this->min]))
            ->setData(["min" => $this->min, "max" => $this->max, "keywordCount" => $this->keywordCount])
            ->build();
    }

    protected function msgIfKeywordTooOften(): array
    {
        return $this->message
            ->setType(CheckResultType::ERROR)
            ->setMsgId(MessageId::KEYWORD_TOO_OFTEN)
            ->setMsg(__('Keywords should not appear in the title more than :max times.', ['max' => $this->max]))
            ->setData(["min" => $this->min, "max" => $this->max, "keywordCount" => $this->keywordCount])
            ->build();
    }

    protected function msgIfEnough(): array
    {
        return $this->message
            ->setType(CheckResultType::SUCCESS)
            ->setMsgId(MessageId::SUCCESS)
            ->setMsg(__('Great! The title contains keywords with a reasonable density.'))
            ->setData(["min" => $this->min, "max" => $this->max, "keywordCount" => $this->keywordCount])
            ->build();
    }
}
