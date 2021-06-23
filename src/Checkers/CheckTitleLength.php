<?php

namespace Qmas\KeywordAnalytics\Checkers;

use Qmas\KeywordAnalytics\Abstracts\Checker;
use Qmas\KeywordAnalytics\CheckingMessage;

class CheckTitleLength extends Checker
{
    private $min;

    private $max;

    protected $title;

    protected $titleCharactersCount = 0;

    public function __construct($title)
    {
        parent::__construct();

        $this->min = config('keyword-analytics.variables.title_length.min');
        $this->max = config('keyword-analytics.variables.title_length.max');

        $this->title = $title;
        $this->titleCharactersCount = strlen($this->title);
    }

    public function check(): Checker
    {
        if ($this->titleCharactersCount === 0) {
            $this->result->push($this->msgIfEmpty());
        }
        elseif ($this->titleCharactersCount < $this->min) {
            $this->result->push($this->msgIfTooShort());
        }
        elseif ($this->titleCharactersCount > $this->max) {
            $this->result->push($this->msgIfTooLong());
        }
        else {
            $this->result->push($this->msgIfOk());
        }

        return $this;
    }

    protected function msgIfEmpty(): array
    {
        return (new CheckingMessage(
            CheckingMessage::IGNORED_TYPE,
            CheckingMessage::TITLE_FIELD,
            CheckingMessage::IGNORE_MSG_ID,
            __('The title is empty.'),
            CheckingMessage::LENGTH_VALIDATOR,
            [
                "length" => 0,
                "min" => $this->min,
                "max" => $this->max
            ]
        ))->build();
    }

    protected function msgIfTooShort(): array
    {
        return (new CheckingMessage(
            CheckingMessage::WARNING_TYPE,
            CheckingMessage::TITLE_FIELD,
            CheckingMessage::TOO_SHORT_MSG_ID,
            __('The page title should be more than :min and less than :max chars long.', [
                'min' => $this->min,
                'max' => $this->max
            ]),
            CheckingMessage::LENGTH_VALIDATOR,
            [
                "length" => $this->titleCharactersCount,
                "min" => $this->min,
                "max" => $this->max
            ]
        ))->build();
    }

    protected function msgIfTooLong(): array
    {
        return (new CheckingMessage(
            CheckingMessage::WARNING_TYPE,
            CheckingMessage::TITLE_FIELD,
            CheckingMessage::TOO_LONG_MSG_ID,
            __('The page title should be more than :min and less than :max chars long.', [
                'min' => $this->min,
                'max' => $this->max
            ]),
            CheckingMessage::LENGTH_VALIDATOR,
            [
                "length" => $this->titleCharactersCount,
                "min" => $this->min,
                "max" => $this->max
            ]
        ))->build();
    }

    protected function msgIfOk(): array
    {
        return (new CheckingMessage(
            CheckingMessage::SUCCESS_TYPE,
            CheckingMessage::TITLE_FIELD,
            CheckingMessage::SUCCESS_MSG_ID,
            __('The page title should be more than :min and less than :max chars long.', [
                'min' => $this->min,
                'max' => $this->max
            ]),
            CheckingMessage::LENGTH_VALIDATOR,
            [
                "length" => $this->titleCharactersCount,
                "min" => $this->min,
                "max" => $this->max
            ]
        ))->build();
    }
}
