<?php

namespace Qmas\KeywordAnalytics\Checkers;

use Qmas\KeywordAnalytics\CheckingMessage;
use Qmas\KeywordAnalytics\Enums\CheckResultType;
use Qmas\KeywordAnalytics\Enums\Field;
use Qmas\KeywordAnalytics\Enums\MessageId;
use Qmas\KeywordAnalytics\Enums\Validator;

class CheckDescriptionLength extends Checker
{
    private int $min;

    private int $max;

    protected string $description;

    protected int $descriptionCharactersCount = 0;

    protected CheckingMessage $message;

    public function __construct($description)
    {
        parent::__construct();

        $this->min = (int) config('keyword-analytics.variables.description_length.min');
        $this->max = (int) config('keyword-analytics.variables.description_length.max');

        $this->description = $description;
        $this->descriptionCharactersCount = mb_strlen($this->description);

        $this->message = CheckingMessage::make()
            ->setValidatorName(Validator::LENGTH)
            ->setField(Field::DESCRIPTION);
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

    protected function msgIfEmpty(): array
    {
        return $this->message
            ->setType(CheckResultType::IGNORED)
            ->setMsgId(MessageId::IGNORE)
            ->setMsg(__('Please consider to add some content to meta description tag.'))
            ->setData(["length" => 0, "min" => $this->min, "max" => $this->max])
            ->build();
    }

    protected function msgIfTooShort(): array
    {
        return $this->message
            ->setType(CheckResultType::WARNING)
            ->setMsgId(MessageId::TOO_SHORT)
            ->setMsg(__('The meta description should be more than :min and less than :max chars.', [
                'min' => $this->min,
                'max' => $this->max
            ]))
            ->setData(["length" => $this->descriptionCharactersCount, "min" => $this->min, "max" => $this->max])
            ->build();
    }

    protected function msgIfTooLong(): array
    {
        return $this->message
            ->setType(CheckResultType::WARNING)
            ->setMsgId(MessageId::TOO_LONG)
            ->setMsg(__('The meta description should be more than :min and less than :max chars.', [
                'min' => $this->min,
                'max' => $this->max
            ]))
            ->setData(["length" => $this->descriptionCharactersCount, "min" => $this->min, "max" => $this->max])
            ->build();
    }

    protected function msgIfOk(): array
    {
        return $this->message
            ->setType(CheckResultType::SUCCESS)
            ->setMsgId(MessageId::SUCCESS)
            ->setMsg(__('The meta description should be more than :min and less than :max chars.', [
                'min' => $this->min,
                'max' => $this->max
            ]))
            ->setData(["length" => $this->descriptionCharactersCount, "min" => $this->min, "max" => $this->max])
            ->build();
    }
}
