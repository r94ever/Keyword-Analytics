<?php

namespace Qmas\KeywordAnalytics\Checkers;

use Qmas\KeywordAnalytics\Abstracts\Checker;
use Qmas\KeywordAnalytics\CheckingMessage;
use Qmas\KeywordAnalytics\Helper;

class CheckKeywordDensity extends Checker
{
    private $min;
    
    private $max;

    protected $keyword;

    protected $contentWithoutHtml;

    protected $keywordCount;

    protected $density;

    public function __construct($contentWithoutHtml, $keyword)
    {
        parent::__construct();

        $this->min = config('keyword-analytics.variables.keyword_density.min');
        $this->max = config('keyword-analytics.variables.keyword_density.max');

        $this->contentWithoutHtml = $contentWithoutHtml;
        $this->keyword = $keyword;
        $this->keywordCount = preg_match_all('/('.$this->keyword.')/im', $this->contentWithoutHtml);
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
        return (new CheckingMessage(
            CheckingMessage::ERROR_TYPE,
            CheckingMessage::HTML_FIELD,
            CheckingMessage::KEYWORD_DENSITY_TOO_LOW_MSG_ID,
            __('The keyword density is :density% which is too low. The keyword was found :count times.', [
                'density' => $this->density,
                'count' => $this->keywordCount
            ]),
            CheckingMessage::KEYWORD_DENSITY_VALIDATOR,
            [
                "min" => $this->min,
                "max" => $this->max,
                "wordCount" => str_word_count($this->contentWithoutHtml),
                "keywordCount" => $this->keywordCount,
                "density" => $this->density
            ]
        ))->build();
    }

    protected function msgIfTooHigh(): array
    {
        return (new CheckingMessage(
            CheckingMessage::ERROR_TYPE,
            CheckingMessage::HTML_FIELD,
            CheckingMessage::KEYWORD_DENSITY_TOO_HIGH_MSG_ID,
            __('The keyword density is :density which is too high. The keyword was found :count times.', [
                'density' => $this->density,
                'count' => $this->keywordCount
            ]),
            CheckingMessage::KEYWORD_DENSITY_VALIDATOR,
            [
                "min" => $this->min,
                "max" => $this->max,
                "wordCount" => str_word_count($this->contentWithoutHtml),
                "keywordCount" => $this->keywordCount,
                "density" => $this->density
            ]
        ))->build();
    }

    protected function msgIfOk(): array
    {
        return (new CheckingMessage(
            CheckingMessage::SUCCESS_TYPE,
            CheckingMessage::HTML_FIELD,
            CheckingMessage::SUCCESS_MSG_ID,
            __('The keyword density is :density% which is very good. The keyword was found :count times.', [
                'density' => $this->density,
                'count' => $this->keywordCount
            ]),
            CheckingMessage::KEYWORD_DENSITY_VALIDATOR,
            [
                "min" => $this->min,
                "max" => $this->max,
                "wordCount" => str_word_count($this->contentWithoutHtml),
                "keywordCount" => $this->keywordCount,
                "density" => $this->density
            ]
        ))->build();
    }
}
