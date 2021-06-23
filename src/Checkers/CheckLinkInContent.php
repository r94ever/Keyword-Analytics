<?php

namespace Qmas\KeywordAnalytics\Checkers;

use Qmas\KeywordAnalytics\Abstracts\Checker;
use Qmas\KeywordAnalytics\CheckingMessage;

class CheckLinkInContent extends Checker
{
    private $min;

    /** @var \PHPHtmlParser\Dom\Collection */
    protected $links;

    /** @var int $linksCount */
    protected $linksCount = 0;

    public function __construct($links)
    {
        parent::__construct();

        $this->min = config('keyword-analytics.variables.link_in_content.min');

        $this->links = $links;
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

    protected function countOutboundLinks()
    {
        $this->links->each(function ($link) {
            /** @var \PHPHtmlParser\Dom\HtmlNode $link */
            $url = $link->getAttribute('href');

            if (filter_var($url, FILTER_VALIDATE_URL)) {
                $this->linksCount += 1;
            }
        });
    }

    protected function msgIfOk(): array
    {
        return (new CheckingMessage(
            CheckingMessage::SUCCESS_TYPE,
            CheckingMessage::HTML_FIELD,
            CheckingMessage::SUCCESS_MSG_ID,
            __('Great. We found :count links will increase your relevance.', ['count' => $this->linksCount]),
            CheckingMessage::OUTBOUND_LINKS_VALIDATOR,
            ['min' => $this->min, 'linkCount' => $this->linksCount]
        ))->build();
    }

    protected function msgIfNoLink(): array
    {
        return (new CheckingMessage(
            CheckingMessage::WARNING_TYPE,
            CheckingMessage::HTML_FIELD,
            CheckingMessage::NO_LINKS_FOUND_MSG_ID,
            __('No Links found. Please consider to add some outgoing links.'),
            CheckingMessage::OUTBOUND_LINKS_VALIDATOR,
            ["min" => $this->min, "linkCount" => 0]
        ))->build();
    }

    protected function msgIfNotEnough(): array
    {
        return (new CheckingMessage(
            CheckingMessage::WARNING_TYPE,
            CheckingMessage::HTML_FIELD,
            CheckingMessage::NO_LINKS_FOUND_MSG_ID,
            __('We found :count links. Please consider to add more at least :remain outgoing links.', [
                'count' => $this->linksCount,
                'remain' => $this->min - $this->linksCount
            ]),
            CheckingMessage::OUTBOUND_LINKS_VALIDATOR,
            ["min" => $this->min, "linkCount" => $this->linksCount]
        ))->build();
    }
}
