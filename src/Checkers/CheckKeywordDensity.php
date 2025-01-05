<?php

namespace Qmas\KeywordAnalytics\Checkers;

use Qmas\KeywordAnalytics\CheckingMessage;
use Qmas\KeywordAnalytics\Enums\CheckResultType;
use Qmas\KeywordAnalytics\Enums\Field;
use Qmas\KeywordAnalytics\Enums\MessageId;
use Qmas\KeywordAnalytics\Enums\Validator;
use Qmas\KeywordAnalytics\Helper;

class CheckKeywordDensity extends Checker
{
    private int $min;
    
    private int $max;

    protected string $keyword;

    protected string $contentWithoutHtml;

    protected int $keywordCount;

    protected float $density;

    protected CheckingMessage $message;

    public function __construct($contentWithoutHtml, $keyword)
    {
        parent::__construct();

        $this->min = (int) config('keyword-analytics.variables.keyword_density.min');
        $this->max = (int) config('keyword-analytics.variables.keyword_density.max');

        $this->contentWithoutHtml = $contentWithoutHtml;
        $this->keyword = $keyword;
        $this->keywordCount = preg_match_all('/('.$this->keyword.')/im', $this->contentWithoutHtml);

        $this->message = CheckingMessage::make()
            ->setValidatorName(Validator::KEYWORD_DENSITY)
            ->setField(Field::HTML);
    }

    public function check(): Checker
    {
        $keywordWord = Helper::countWords($this->keyword);
        $pageWord = Helper::countWords($this->contentWithoutHtml);

        $this->density = round(
            ($this->keywordCount / ($pageWord - ($this->keywordCount * ($keywordWord - 1)))) * 100,
            2
        );

        if ($this->density < $this->min) {
            $this->result->push($this->msgIfTooLow());
        }
        elseif ($this->density > $this->max) {
            $this->result->push($this->msgIfTooHigh());
        }
        else {
            $this->result->push($this->msgIfOk());
        }

        return $this;
    }

    protected function msgIfTooLow(): array
    {
        return $this->message
            ->setType(CheckResultType::ERROR)
            ->setMsgId(MessageId::KEYWORD_DENSITY_TOO_LOW)
            ->setMsg(__('The keyword density is :density% which is too low. The keyword was found :count times.', [
                'density' => $this->density,
                'count' => $this->keywordCount
            ]))
            ->setData([
                "min" => $this->min,
                "max" => $this->max,
                "wordCount" => str_word_count($this->contentWithoutHtml),
                "keywordCount" => $this->keywordCount,
                "density" => $this->density
            ])
            ->build();
    }

    protected function msgIfTooHigh(): array
    {
        return $this->message
            ->setType(CheckResultType::ERROR)
            ->setMsgId(MessageId::KEYWORD_DENSITY_TOO_HIGH)
            ->setMsg(__('The keyword density is :density% which is too high. The keyword was found :count times.', [
                'density' => $this->density,
                'count' => $this->keywordCount
            ]))
            ->setData([
                "min" => $this->min,
                "max" => $this->max,
                "wordCount" => str_word_count($this->contentWithoutHtml),
                "keywordCount" => $this->keywordCount,
                "density" => $this->density
            ])
            ->build();
    }

    protected function msgIfOk(): array
    {
        return $this->message
            ->setType(CheckResultType::SUCCESS)
            ->setMsgId(MessageId::SUCCESS)
            ->setMsg(__('The keyword density is :density% which is very good. The keyword was found :count times.', [
                'density' => $this->density,
                'count' => $this->keywordCount
            ]))
            ->setData([
                "min" => $this->min,
                "max" => $this->max,
                "wordCount" => str_word_count($this->contentWithoutHtml),
                "keywordCount" => $this->keywordCount,
                "density" => $this->density
            ])
            ->build();
    }
}
