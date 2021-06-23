<?php

namespace Qmas\KeywordAnalytics;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Request;
use PHPHtmlParser\Dom;
use PHPHtmlParser\Options;
use Qmas\KeywordAnalytics\Checkers\CheckContentLength;
use Qmas\KeywordAnalytics\Checkers\CheckDescriptionLength;
use Qmas\KeywordAnalytics\Checkers\CheckHeadingInContent;
use Qmas\KeywordAnalytics\Checkers\CheckImageInContent;
use Qmas\KeywordAnalytics\Checkers\CheckKeywordDensity;
use Qmas\KeywordAnalytics\Checkers\CheckKeywordInDescription;
use Qmas\KeywordAnalytics\Checkers\CheckKeywordInFirstParagraph;
use Qmas\KeywordAnalytics\Checkers\CheckKeywordInHeading;
use Qmas\KeywordAnalytics\Checkers\CheckKeywordInImgAlt;
use Qmas\KeywordAnalytics\Checkers\CheckKeywordInLinkTitle;
use Qmas\KeywordAnalytics\Checkers\CheckKeywordInTitle;
use Qmas\KeywordAnalytics\Checkers\CheckKeywordInUrl;
use Qmas\KeywordAnalytics\Checkers\CheckKeywordLength;
use Qmas\KeywordAnalytics\Checkers\CheckLinkInContent;
use Qmas\KeywordAnalytics\Checkers\CheckTitleLength;

class Analysis
{
    /** @var bool $isFromRequest */
    protected $isFromRequest = false;

    /** @var string $keyword */
    protected $keyword;

    /** @var string $title */
    protected $title;

    /** @var string $description */
    protected $description;

    /** @var string $url */
    protected $url;

    /** @var string $html */
    protected $html;

    /** @var string $content */
    protected $content;

    /** @var Dom $dom */
    protected $dom;

    /** @var Dom\Node\Collection $headings */
    protected $headings;

    /** @var Dom\Node\Collection $images */
    protected $images;

    /** @var Dom\Node\Collection $links */
    protected $links;

    /** @var Collection $results */
    protected $results;

    /**
     * Analysis constructor.
     */
    public function __construct()
    {
        $this->results = collect();
    }

    /**
     * Capture input data from request
     *
     * @return $this
     */
    public function fromRequest(): Analysis
    {
        $this->prepareData(
            Request::input(config('keyword-analytics.request_keys.keyword')),
            Request::input(config('keyword-analytics.request_keys.title')),
            Request::input(config('keyword-analytics.request_keys.description')),
            Request::input(config('keyword-analytics.request_keys.html')),
            Request::input(config('keyword-analytics.request_keys.url'))
        );

        $this->isFromRequest = true;

        return $this;
    }

    public function prepareData(
        string $keyword,
        string $title,
        string $description,
        string $html,
        string $url
    ) {
        $this->keyword      = Helper::unicodeToAscii($keyword);
        $this->title        = Helper::unicodeToAscii($title);
        $this->description  = Helper::unicodeToAscii($description);
        $this->url          = Helper::unicodeToAscii(str_replace(['-', '_', '/'], ' ', $url));
        $this->html         = $this->stripUnnecessaryTags($html);
        $this->content      = Helper::unicodeToAscii(Helper::stripHtmlTags($this->html));

//        dump(
//            $this->keyword,
//            $this->title,
//            $this->description,
//            $this->url,
//            $this->html,
//            $this->content
//        );
    }

    public function getResults(): Collection
    {
        return $this->results;
    }

    /**
     * Strip HTML & BODY tags
     * @param $html
     * @return string
     */
    protected function stripUnnecessaryTags($html): string
    {
        // Remove <head> tag and its content
        $html = preg_replace(
            '/<head(?:\s+[a-z]+(?:\s*=\s*(?:"[^"]*"|\'[^\']*\'|[^\s>]+)))*\s*>([\S\s]*)<\/head>/m',
            '',
            $html
        );

        return str_replace(['<html>', '</html>', '<body>', '</body>'], "", $html);
    }

    /**
     * @return Collection
     */
    protected function findHeadingInHtml(): Collection
    {
        $search = preg_match_all('/<(h1|h2|h3|h4|h5|h6)>(.*?)<\/\1>/is', $this->html, $matches);

        return $search > 1 ? collect($matches[2]) : collect([]);
    }

    protected function findDomNodes()
    {
        $this->dom = new Dom();
        $this->dom->loadStr($this->html);

        $this->images       = $this->dom->find('img');
        $this->headings     = $this->dom->find('h1,h2,h3,h4,h5,h6');
        $this->links        = $this->dom->find('a');
    }

    public function run(
        string $keyword = '',
        string $title = '',
        string $description = '',
        string $html = '',
        string $url = ''
    ): Analysis
    {
        if (! $this->isFromRequest) {
            $this->prepareData($keyword, $title, $description, $html, $url);
        }

        $this->findDomNodes();

        $this->checkKeywordLength()
            ->checkTitleLength()
            ->checkKeywordInTitle()
            ->checkDescriptionLength()
            ->checkKeywordInDescription()
            ->checkContentLength()
            ->checkKeywordInFirstParagraph()
            ->checkHeadingInContent()
            ->checkKeywordInHeading()
            ->checkImageInContent()
            ->checkKeywordInImageAlt()
            ->checkLinkInContent()
            ->checkKeywordInLinkTitle()
            ->checkKeywordDensity()
            ->checkKeywordInUrl();

        return $this;
    }

    protected function checkKeywordLength(): Analysis
    {
        $checker = new CheckKeywordLength($this->keyword);
        $result = $checker->check()->getResult();

        $this->results = $this->results->concat($result);

        return $this;
    }

    protected function checkTitleLength(): Analysis
    {
        $checker = new CheckTitleLength($this->title);
        $result = $checker->check()->getResult();

        $this->results = $this->results->concat($result);

        return $this;
    }

    protected function checkKeywordInTitle(): Analysis
    {
        $checker = new CheckKeywordInTitle($this->keyword, $this->title);
        $result = $checker->check()->getResult();

        $this->results = $this->results->concat($result);

        return $this;
    }

    protected function checkDescriptionLength(): Analysis
    {
        $checker = new CheckDescriptionLength($this->description);
        $result = $checker->check()->getResult();

        $this->results = $this->results->concat($result);

        return $this;
    }

    protected function checkKeywordInDescription(): Analysis
    {
        if ($this->description) {
            $checker = new CheckKeywordInDescription($this->keyword, $this->description);
            $result = $checker->check()->getResult();

            $this->results = $this->results->concat($result);
        }

        return $this;
    }

    protected function checkContentLength(): Analysis
    {
        $checker = new CheckContentLength($this->content);
        $result = $checker->check()->getResult();

        $this->results = $this->results->concat($result);

        return $this;
    }

    protected function checkKeywordInFirstParagraph(): Analysis
    {
        $checker = new CheckKeywordInFirstParagraph($this->keyword, $this->html);
        $result = $checker->check()->getResult();

        $this->results = $this->results->concat($result);

        return $this;
    }

    protected function checkHeadingInContent(): Analysis
    {
        if ($this->html) {
            $checker = new CheckHeadingInContent($this->headings);
            $result = $checker->check()->getResult();

            $this->results = $this->results->concat($result);
        }

        return $this;
    }

    protected function checkKeywordInHeading(): Analysis
    {
        if ($this->html) {
            $checker = new CheckKeywordInHeading($this->keyword, $this->headings);
            $result = $checker->check()->getResult();

            $this->results = $this->results->concat($result);
        }

        return $this;
    }

    protected function checkImageInContent(): Analysis
    {
        if ($this->html) {
            $checker = new CheckImageInContent($this->images);
            $result = $checker->check()->getResult();

            $this->results = $this->results->concat($result);
        }

        return $this;
    }

    protected function checkKeywordInImageAlt(): Analysis
    {
        if ($this->images->count() >= 1) {
            $checker = new CheckKeywordInImgAlt($this->images, $this->keyword);
            $result = $checker->check()->getResult();

            $this->results = $this->results->concat($result);
        }

        return $this;
    }

    protected function checkLinkInContent(): Analysis
    {
        if ($this->html) {
            $checker = new CheckLinkInContent($this->links);
            $result = $checker->check()->getResult();

            $this->results = $this->results->concat($result);
        }

        return $this;
    }

    protected function checkKeywordInLinkTitle(): Analysis
    {
        if ($this->links->count() > 0) {
            $checker = new CheckKeywordInLinkTitle($this->links, $this->keyword);
            $result = $checker->check()->getResult();

            $this->results = $this->results->concat($result);
        }

        return $this;
    }

    protected function checkKeywordDensity(): Analysis
    {
        if ($this->html) {
            $checker = new CheckKeywordDensity($this->content, $this->keyword);
            $result = $checker->check()->getResult();

            $this->results = $this->results->concat($result);
        }

        return $this;
    }

    protected function checkKeywordInUrl(): Analysis
    {
        $checker = new CheckKeywordInUrl($this->url, $this->keyword);
        $result = $checker->check()->getResult();

        $this->results = $this->results->concat($result);

        return $this;
    }
}
