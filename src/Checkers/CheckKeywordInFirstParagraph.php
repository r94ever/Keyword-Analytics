<?php

namespace Qmas\KeywordAnalytics\Checkers;

use Qmas\KeywordAnalytics\CheckingMessage;
use Qmas\KeywordAnalytics\Enums\CheckResultType;
use Qmas\KeywordAnalytics\Enums\Field;
use Qmas\KeywordAnalytics\Enums\MessageId;
use Qmas\KeywordAnalytics\Enums\Validator;
use Qmas\KeywordAnalytics\Helper;

class CheckKeywordInFirstParagraph extends Checker
{
    private int $min;
    
    private int $max;

    protected string $keyword;

    protected string $html;

    protected string $firstPara;

    protected int $keywordCount;

    protected CheckingMessage $message;

    public function __construct($keyword, $html)
    {
        parent::__construct();

        $this->min = (int) config('keyword-analytics.variables.keyword_in_first_paragraph.min');
        $this->max = (int) config('keyword-analytics.variables.keyword_in_first_paragraph.max');

        $this->keyword = $keyword;
        $this->html = $html;
        $this->firstPara = $this->getFirstParagraph();

        $this->message = CheckingMessage::make()
            ->setValidatorName(Validator::KEYWORD_COUNT)
            ->setField(Field::HTML);
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
        return $this->message
            ->setType(CheckResultType::IGNORED)
            ->setMsgId(MessageId::IGNORE)
            ->setData(["min" => $this->min, "max" => $this->max])
            ->build();
    }

    protected function msgIfKeywordNotFound(): array
    {
        return $this->message
            ->setType(CheckResultType::ERROR)
            ->setMsgId(MessageId::KEYWORD_NOT_FOUND)
            ->setMsg(__('The first paragraph of content does not contain the keyword.'))
            ->setData(["min" => $this->min, "max" => $this->max, "keywordCount" => 0])
            ->build();
    }

    protected function msgIfKeywordTooLow(): array
    {
        return $this->message
            ->setType(CheckResultType::WARNING)
            ->setMsgId(MessageId::KEYWORD_TOO_LOW)
            ->setMsg(__('The first paragraph of content contains the keyword less than :min times.', [
                'min' => $this->min
            ]))
            ->setData(["min" => $this->min, "max" => $this->max, "keywordCount" => $this->keywordCount])
            ->build();
    }

    protected function msgIfKeywordTooOften(): array
    {
        return $this->message
            ->setType(CheckResultType::WARNING)
            ->setMsgId(MessageId::KEYWORD_TOO_OFTEN)
            ->setMsg(__('The first paragraph of content contains the keyword more than :max times.', [
                'max' => $this->max
            ]))
            ->setData(["min" => $this->min, "max" => $this->max, "keywordCount" => $this->keywordCount])
            ->build();
    }

    protected function msgIfEnough(): array
    {
        return $this->message
            ->setType(CheckResultType::SUCCESS)
            ->setMsgId(MessageId::SUCCESS)
            ->setMsg(__('The first paragraph of content should contain the keyword.'))
            ->setData(["min" => $this->min, "max" => $this->max, "keywordCount" => $this->keywordCount])
            ->build();
    }
}
