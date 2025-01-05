<?php

namespace Qmas\KeywordAnalytics\Checkers;

use Illuminate\Support\Collection;
use Qmas\KeywordAnalytics\CheckingMessage;
use Qmas\KeywordAnalytics\Enums\CheckResultType;
use Qmas\KeywordAnalytics\Enums\Field;
use Qmas\KeywordAnalytics\Enums\MessageId;
use Qmas\KeywordAnalytics\Enums\Validator;

class CheckImageInContent extends Checker
{
    private int $min;

    /** @var Collection $images */
    protected Collection $images;

    /** @var int $imagesCount */
    protected int $imagesCount;

    /** @var CheckingMessage $message */
    protected CheckingMessage $message;

    public function __construct($images)
    {
        parent::__construct();

        $this->min = (int) config('keyword-analytics.variables.image_in_content.min');

        $this->images = $images;
        $this->imagesCount = $this->images->count();

        $this->message = CheckingMessage::make()
            ->setValidatorName(Validator::IMAGE_COUNT)
            ->setField(Field::HTML);
    }

    public function check(): Checker
    {
        if ($this->imagesCount === 0) {
            $this->result->push($this->msgIfNoImage());
        }
        elseif ($this->imagesCount < $this->min) {
            $this->result->push($this->msgIfNotEnough());
        }
        else {
            $this->result->push($this->msgIfContainImage());
        }

        return $this;
    }

    protected function msgIfContainImage(): array
    {
        return $this->message
            ->setType(CheckResultType::SUCCESS)
            ->setMsgId(MessageId::SUCCESS)
            ->setMsg(__('Great. We found :count images. This will increase your relevance.', [
                'count' => $this->images->count()
            ]))
            ->setData(['min' => $this->min, 'imageCount' => $this->imagesCount])
            ->build();
    }

    protected function msgIfNotEnough(): array
    {
        return $this->message
            ->setType(CheckResultType::WARNING)
            ->setMsgId(MessageId::SUCCESS)
            ->setMsg(__('Too few images. Please consider adding at least :remain images to your content to increase the relevance.'))
            ->setData(['min' => $this->min, 'imageCount' => $this->imagesCount])
            ->build();
    }

    protected function msgIfNoImage(): array
    {
        return $this->message
            ->setType(CheckResultType::WARNING)
            ->setMsgId(MessageId::NO_IMAGE)
            ->setMsg(__('No image found in content. Please consider to add at least :min images to your content.', [
                'min' => $this->min
            ]))
            ->setData(['min' => $this->min, 'imageCount' => $this->imagesCount])
            ->build();
    }
}
