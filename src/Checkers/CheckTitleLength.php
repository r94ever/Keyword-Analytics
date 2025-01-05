<?php

namespace Qmas\KeywordAnalytics\Checkers;

use Qmas\KeywordAnalytics\CheckingMessage;
use Qmas\KeywordAnalytics\Enums\CheckResultType;
use Qmas\KeywordAnalytics\Enums\Field;
use Qmas\KeywordAnalytics\Enums\MessageId;
use Qmas\KeywordAnalytics\Enums\Validator;

class CheckTitleLength extends Checker
{
    private int $min;

    private int $max;

    protected string $title;

    protected int $titleCharactersCount = 0;

    protected CheckingMessage $message;

    public function __construct($title)
    {
        parent::__construct();

        $this->min = (int) config('keyword-analytics.variables.title_length.min');
        $this->max = (int) config('keyword-analytics.variables.title_length.max');

        $this->title = $title;
        $this->titleCharactersCount = strlen($this->title);

        $this->message = CheckingMessage::make()
            ->setValidatorName(Validator::LENGTH)
            ->setField(Field::TITLE);
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
        return $this->message
            ->setType(CheckResultType::IGNORED)
            ->setMsgId(MessageId::IGNORE)
            ->setMsg(__('The title is empty.'))
            ->setData(["length" => 0, "min" => $this->min, "max" => $this->max])
            ->build();
    }

    protected function msgIfTooShort(): array
    {
        return $this->message
            ->setType(CheckResultType::WARNING)
            ->setMsgId(MessageId::TOO_SHORT)
            ->setMsg(__('The page title should be more than :min and less than :max chars long.', [
                'min' => $this->min,
                'max' => $this->max
            ]))
            ->setData(["length" => 0, "min" => $this->min, "max" => $this->max])
            ->build();
    }

    protected function msgIfTooLong(): array
    {
        return $this->message
            ->setType(CheckResultType::WARNING)
            ->setMsgId(MessageId::TOO_LONG)
            ->setMsg(__('The page title should be more than :min and less than :max chars long.', [
                'min' => $this->min,
                'max' => $this->max
            ]))
            ->setData(["length" => $this->titleCharactersCount, "min" => $this->min, "max" => $this->max])
            ->build();
    }

    protected function msgIfOk(): array
    {
        return $this->message
            ->setType(CheckResultType::SUCCESS)
            ->setMsgId(MessageId::SUCCESS)
            ->setMsg(__('The page title should be more than :min and less than :max chars long.', [
                'min' => $this->min,
                'max' => $this->max
            ]))
            ->setData(["length" => 0, "min" => $this->min, "max" => $this->max])
            ->build();
    }
}
