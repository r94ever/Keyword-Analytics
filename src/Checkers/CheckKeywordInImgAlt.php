<?php

namespace Qmas\KeywordAnalytics\Checkers;

use Illuminate\Support\Str;
use Qmas\KeywordAnalytics\CheckingMessage;
use Qmas\KeywordAnalytics\Enums\CheckResultType;
use Qmas\KeywordAnalytics\Enums\Field;
use Qmas\KeywordAnalytics\Enums\MessageId;
use Qmas\KeywordAnalytics\Enums\Validator;
use Qmas\KeywordAnalytics\Helper;

class CheckKeywordInImgAlt extends CheckImageInContent
{
    private int $min;

    protected string $keyword;

    protected int $keywordCount = 0;

    protected CheckingMessage $message;

    public function __construct($images, $keyword)
    {
        parent::__construct($images);

        $this->min = (int) config('keyword-analytics.variables.keyword_in_alt_image.min');

        $this->keyword = $keyword;

        $this->message = CheckingMessage::make()
            ->setValidatorName(Validator::KEYWORD_COUNT)
            ->setField(Field::HTML);
    }

    public function check(): Checker
    {
        $this->countKeyword();

        if ($this->keywordCount === 0) {
            $this->result->push($this->msgIfNotContain());
        }
        elseif ($this->keywordCount < $this->min) {
            $this->result->push($this->msgIfTooLow());
        }
        else {
            $this->result->push($this->msgIfContain());
        }

        return $this;
    }

    protected function countKeyword(): void
    {
        foreach ($this->images as $image) {
            $alt = str($image->attr('alt'))->transliterate();

            if (Str::contains($alt, $this->keyword)) {
                $this->keywordCount += 1;
            }
        }
    }

    protected function msgIfContain(): array
    {
        return $this->message
            ->setType(CheckResultType::SUCCESS)
            ->setMsgId(MessageId::SUCCESS)
            ->setMsg(__('Great. The ALT attribute in your IMG tags containing the keyword.'))
            ->setData(['min' => $this->min, 'keywordCount' => $this->keywordCount])
            ->build();
    }

    protected function msgIfTooLow(): array
    {
        return $this->message
            ->setType(CheckResultType::WARNING)
            ->setMsgId(MessageId::KEYWORD_TOO_LOW)
            ->setMsg(__('The keyword should appear in the ALT attribute of at least :min IMG tags', [
                'min' => $this->min
            ]))
            ->setData(['min' => $this->min, 'keywordCount' => $this->keywordCount])
            ->build();
    }

    protected function msgIfNotContain(): array
    {
        return $this->message
            ->setType(CheckResultType::ERROR)
            ->setMsgId(MessageId::KEYWORD_NOT_FOUND)
            ->setMsg(__('The ALT attribute in your IMG tags should contain the keyword.'))
            ->setData(['min' => $this->min, 'keywordCount' => 0])
            ->build();
    }
}
