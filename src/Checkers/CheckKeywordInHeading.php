<?php

namespace Qmas\KeywordAnalytics\Checkers;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Qmas\KeywordAnalytics\CheckingMessage;
use Qmas\KeywordAnalytics\Enums\CheckResultType;
use Qmas\KeywordAnalytics\Enums\Field;
use Qmas\KeywordAnalytics\Enums\MessageId;
use Qmas\KeywordAnalytics\Enums\Validator;
use Qmas\KeywordAnalytics\Helper;
use Symfony\Component\DomCrawler\Crawler;

class CheckKeywordInHeading extends Checker
{
    private int $min;

    /** @var string $keyword */
    protected string $keyword;

    /** @var Collection|Crawler[] */
    protected Collection|array $headings;

    protected int $headingsContainKeyword = 0;

    protected CheckingMessage $message;

    public function __construct($keyword, $headings)
    {
        parent::__construct();

        $this->min = (int) config('keyword-analytics.variables.keyword_in_heading.min');

        $this->keyword = $keyword;
        $this->headings = $headings;

        $this->message = CheckingMessage::make()
            ->setValidatorName(Validator::HEADING)
            ->setField(Field::HTML);
    }

    public function check(): Checker
    {
        foreach ($this->headings as $heading) {
            $innerHeading = Helper::unicodeToAscii($heading->innerText());

            if (Str::contains($innerHeading, $this->keyword)) {
                $this->headingsContainKeyword += 1;
            }
        }

        if ($this->headingsContainKeyword > 0) {
            $this->result->push($this->msgIfContained());
        }
        else {
            $this->result->push($this->msgIfEmpty());
        }

        return $this;
    }

    protected function msgIfEmpty(): array
    {
        return $this->message
            ->setType(CheckResultType::ERROR)
            ->setMsgId(MessageId::KEYWORD_NOT_FOUND)
            ->setMsg(__('No heading containing keyword was detected.'))
            ->setData(['min' => $this->min, 'keywordCount' => 0])
            ->build();
    }

    protected function msgIfContained(): array
    {
        return $this->message
            ->setType(CheckResultType::SUCCESS)
            ->setMsgId(MessageId::SUCCESS)
            ->setMsg(__('Your headings should contain the keyword.'))
            ->setData(['min' => $this->min, 'headingCount' => $this->headingsContainKeyword])
            ->build();
    }
}
