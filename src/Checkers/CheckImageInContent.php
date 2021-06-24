<?php

namespace Qmas\KeywordAnalytics\Checkers;

use PHPHtmlParser\Dom\Node\Collection;
use Qmas\KeywordAnalytics\Abstracts\Checker;
use Qmas\KeywordAnalytics\CheckingMessage;

class CheckImageInContent extends Checker
{
    private $min;

    /** @var Collection $images */
    protected $images;

    /** @var int $imagesCount */
    protected $imagesCount;

    public function __construct($images)
    {
        parent::__construct();

        $this->min = config('keyword-analytics.variables.image_in_content.min');

        $this->images = $images;
        $this->imagesCount = $this->images->count();
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
        return (new CheckingMessage(
            CheckingMessage::SUCCESS_TYPE,
            CheckingMessage::HTML_FIELD,
            CheckingMessage::SUCCESS_MSG_ID,
            __('Great. We found :count images. This will increase your relevance.', ['count' => $this->images->count()]),
            CheckingMessage::IMAGE_COUNT_VALIDATOR,
            ['min' => $this->min, 'imageCount' => $this->imagesCount]
        ))->build();
    }

    protected function msgIfNotEnough(): array
    {
        $remain = $this->min - $this->imagesCount;

        return (new CheckingMessage(
            CheckingMessage::WARNING_TYPE,
            CheckingMessage::HTML_FIELD,
            CheckingMessage::SUCCESS_MSG_ID,
            __('Too few images. Please consider adding at least :remain images to your content to increase the relevance.'),
            CheckingMessage::TOO_FEW_IMAGES_MSG_ID,
            ['min' => $this->min, 'imageCount' => $this->imagesCount]
        ))->build();
    }

    protected function msgIfNoImage(): array
    {
        return (new CheckingMessage(
            CheckingMessage::WARNING_TYPE,
            CheckingMessage::HTML_FIELD,
            CheckingMessage::NO_IMAGE_MSG_ID,
            __('No image found in content. Please consider to add at least :min images to your content.', ['min' => $this->min]),
            CheckingMessage::IMAGE_COUNT_VALIDATOR,
            ['min' => $this->min, 'imageCount' => $this->imagesCount]
        ))->build();
    }
}
