<?php


namespace Qmas\KeywordAnalytics\Checkers;


use Qmas\KeywordAnalytics\Abstracts\Checker;
use Qmas\KeywordAnalytics\CheckingMessage;

class CheckDescriptionLength extends Checker
{
    private $min;

    private $max;

    protected $description;

    protected $descriptionCharactersCount = 0;

    public function __construct($description)
    {
        parent::__construct();

        $this->min = config('keyword-analytics.variables.description_length.min');
        $this->max = config('keyword-analytics.variables.description_length.max');

        $this->description = $description;
        $this->descriptionCharactersCount = mb_strlen($this->description);
    }

    public function check(): Checker
    {
        if ($this->descriptionCharactersCount === 0) {
            $this->result->push($this->msgIfEmpty());
        }
        elseif ($this->descriptionCharactersCount < $this->min) {
            $this->result->push($this->msgIfTooShort());
        }
        elseif ($this->descriptionCharactersCount > $this->max) {
            $this->result->push($this->msgIfTooLong());
        }
        else {
            $this->result->push($this->msgIfOk());
        }

        return $this;
    }

    protected function msgIfEmpty()
    {
        return (new CheckingMessage(
            CheckingMessage::IGNORED_TYPE,
            CheckingMessage::DESCRIPTION_FIELD,
            CheckingMessage::IGNORE_MSG_ID,
            __('Please consider to add some content to meta description tag.'),
            CheckingMessage::LENGTH_VALIDATOR,
            [
                "length" => 0,
                "min" => $this->min,
                "max" => $this->max
            ]
        ))->build();
    }

    protected function msgIfTooShort()
    {
        return (new CheckingMessage(
            CheckingMessage::WARNING_TYPE,
            CheckingMessage::DESCRIPTION_FIELD,
            CheckingMessage::TOO_SHORT_MSG_ID,
            __('The meta description should be more than :min and less than :max chars.', [
                'min' => $this->min,
                'max' => $this->max
            ]),
            CheckingMessage::LENGTH_VALIDATOR,
            [
                "length" => $this->descriptionCharactersCount,
                "min" => $this->min,
                "max" => $this->max
            ]
        ))->build();
    }

    protected function msgIfTooLong()
    {
        return (new CheckingMessage(
            CheckingMessage::WARNING_TYPE,
            CheckingMessage::DESCRIPTION_FIELD,
            CheckingMessage::TOO_LONG_MSG_ID,
            __('The meta description should be more than :min and less than :max chars.', [
                'min' => $this->min,
                'max' => $this->max
            ]),
            CheckingMessage::LENGTH_VALIDATOR,
            [
                "length" => $this->descriptionCharactersCount,
                "min" => $this->min,
                "max" => $this->max
            ]
        ))->build();
    }

    protected function msgIfOk()
    {
        return (new CheckingMessage(
            CheckingMessage::SUCCESS_TYPE,
            CheckingMessage::DESCRIPTION_FIELD,
            CheckingMessage::SUCCESS_MSG_ID,
            __('The meta description should be more than :min and less than :max chars.', [
                'min' => $this->min,
                'max' => $this->max
            ]),
            CheckingMessage::LENGTH_VALIDATOR,
            [
                "length" => $this->descriptionCharactersCount,
                "min" => $this->min,
                "max" => $this->max
            ]
        ))->build();
    }

}
