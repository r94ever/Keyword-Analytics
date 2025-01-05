<?php

namespace Qmas\KeywordAnalytics\Checkers;

use Qmas\KeywordAnalytics\CheckingMessage;
use Qmas\KeywordAnalytics\Enums\CheckResultType;
use Qmas\KeywordAnalytics\Enums\Field;
use Qmas\KeywordAnalytics\Enums\MessageId;
use Qmas\KeywordAnalytics\Enums\Validator;
use Qmas\KeywordAnalytics\Helper;

class CheckContentLength extends Checker
{
    private int $min;

    protected string $contentWithoutHtml;

    protected int $htmlWordsCount = 0;

    protected CheckingMessage $message;

    public function __construct($contentWithoutHtml)
    {
        parent::__construct();

        $this->min = (int) config('keyword-analytics.variables.content_length.min');
        $this->contentWithoutHtml = $contentWithoutHtml;
        $this->htmlWordsCount = Helper::countWords($this->contentWithoutHtml);

        $this->message = CheckingMessage::make()
            ->setValidatorName(Validator::WORD_COUNT)
            ->setField(Field::HTML);
    }

    public function check(): Checker
    {
        if (! $this->contentWithoutHtml) {
            $this->result->push($this->msgIfEmpty());
        }
        elseif ($this->htmlWordsCount < $this->min) {
            $this->result->push($this->msgIfTooShort());
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
            ->setData(["min" => $this->min, "wordCount" => $this->htmlWordsCount])
            ->build();
    }

    protected function msgIfTooShort(): array
    {
        return $this->message
            ->setType(CheckResultType::WARNING)
            ->setMsgId(MessageId::TOO_SHORT)
            ->setData(["min" => $this->min, "wordCount" => $this->htmlWordsCount])
            ->setMsg(__('The content should contain more than :min words to be recognized as relevant.', [
                'min' => $this->min
            ]))
            ->build();
    }

    protected function msgIfOk(): array
    {
        return $this->message
            ->setType(CheckResultType::SUCCESS)
            ->setMsgId(MessageId::SUCCESS)
            ->setMsg(__('Great. The content contains more than :min words.', ['min' => $this->min]))
            ->setData(["min" => $this->min, "wordCount" => $this->htmlWordsCount])
            ->build();
    }
}
