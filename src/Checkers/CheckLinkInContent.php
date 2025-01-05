<?php

namespace Qmas\KeywordAnalytics\Checkers;

use Illuminate\Support\Collection;
use Qmas\KeywordAnalytics\CheckingMessage;
use Qmas\KeywordAnalytics\Enums\CheckResultType;
use Qmas\KeywordAnalytics\Enums\Field;
use Qmas\KeywordAnalytics\Enums\MessageId;
use Qmas\KeywordAnalytics\Enums\Validator;

class CheckLinkInContent extends Checker
{
    private int $min;

    /** @var Collection */
    protected Collection $links;

    /** @var int $linksCount */
    protected int $linksCount = 0;

    protected CheckingMessage $message;

    public function __construct($links)
    {
        parent::__construct();

        $this->min = (int) config('keyword-analytics.variables.link_in_content.min');

        $this->links = $links;

        $this->message = CheckingMessage::make()
            ->setValidatorName(Validator::OUTBOUND_LINKS)
            ->setField(Field::HTML);

        $this->countOutboundLinks();
    }

    public function check(): Checker
    {
        if ($this->linksCount === 0) {
            $this->result->push($this->msgIfNoLink());
        }
        elseif ($this->linksCount < $this->min) {
            $this->result->push($this->msgIfNotEnough());
        }
        else {
            $this->result->push($this->msgIfOk());
        }

        return $this;
    }

    protected function countOutboundLinks(): void
    {
        foreach ($this->links as $link) {
            $url = $link->attr('href');

            if (filter_var($url, FILTER_VALIDATE_URL)) {
                $this->linksCount += 1;
            }
        }
    }

    protected function msgIfOk(): array
    {
        return $this->message
            ->setType(CheckResultType::SUCCESS)
            ->setMsgId(MessageId::SUCCESS)
            ->setMsg(__('Great. We found :count links will increase your relevance.', [
                'count' => $this->linksCount
            ]))
            ->setData(['min' => $this->min, 'linkCount' => $this->linksCount])
            ->build();
    }

    protected function msgIfNoLink(): array
    {
        return $this->message
            ->setType(CheckResultType::WARNING)
            ->setMsgId(MessageId::NO_LINKS_FOUND)
            ->setMsg(__('No Links found. Please consider to add some outgoing links.'))
            ->setData(['min' => $this->min, 'linkCount' => 0])
            ->build();
    }

    protected function msgIfNotEnough(): array
    {
        return $this->message
            ->setType(CheckResultType::WARNING)
            ->setMsgId(MessageId::NO_LINKS_FOUND)
            ->setMsg(__('We found :count links. Please consider to add more at least :remain outgoing links.', [
                'count' => $this->linksCount,
                'remain' => $this->min - $this->linksCount
            ]))
            ->setData(['min' => $this->min, 'linkCount' => $this->linksCount])
            ->build();
    }
}
