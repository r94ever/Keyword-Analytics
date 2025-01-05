<?php

namespace Qmas\KeywordAnalytics\Checkers;

use Illuminate\Support\Collection;
use Qmas\KeywordAnalytics\CheckingMessage;
use Qmas\KeywordAnalytics\Enums\CheckResultType;
use Qmas\KeywordAnalytics\Enums\Field;
use Qmas\KeywordAnalytics\Enums\MessageId;
use Qmas\KeywordAnalytics\Enums\Validator;

class CheckHeadingInContent extends Checker
{
    private int $min;

    /** @var Collection $headings */
    protected Collection $headings;

    /** @var int $headingCount */
    protected int $headingCount = 0;

    /** @var CheckingMessage $message */
    protected CheckingMessage $message;

    /**
     * CheckHeadingInContent constructor.
     * @param Collection $headings
     */
    public function __construct(Collection $headings)
    {
        parent::__construct();

        $this->min = (int) config('keyword-analytics.variables.heading_in_content.min');

        $this->headings = $headings;
        $this->headingCount = count($headings);

        $this->message = CheckingMessage::make()
            ->setValidatorName(Validator::HEADING)
            ->setField(Field::HTML);
    }

    public function check(): Checker
    {
        if ($this->headingCount === 0) {
            $this->result->push($this->msgIfEmpty());
        }
        elseif ($this->headingCount < $this->min) {
            $this->result->push($this->msgIfTooLow());
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
            ->setMsg(__('The content should contain heading tags.'))
            ->setData(["headingCount" => $this->headingCount, "min" => $this->min])
            ->build();
    }

    protected function msgIfTooLow(): array
    {
        return $this->message
            ->setType(CheckResultType::ERROR)
            ->setMsgId(MessageId::IGNORE)
            ->setMsg(__('The content should contain at least :num heading tags.', ['num' => $this->min]))
            ->setData(["headingCount" => $this->headingCount, "min" => $this->min])
            ->build();
    }

    protected function msgIfOk(): array
    {
        return $this->message
            ->setType(CheckResultType::SUCCESS)
            ->setMsgId(MessageId::SUCCESS)
            ->setMsg(__('The content is containing one or more heading tags.'))
            ->setData(["headingCount" => $this->headingCount, "min" => $this->min])
            ->build();
    }
}
