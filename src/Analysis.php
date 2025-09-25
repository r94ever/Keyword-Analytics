<?php

namespace Qmas\KeywordAnalytics;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Request;
use Symfony\Component\DomCrawler\Crawler;
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
use Qmas\KeywordAnalytics\Exceptions\KeywordNotSetException;

class Analysis
{
    /** @var bool $isFromRequest */
    protected bool $isFromRequest = false;

    /** @var string $keyword */
    protected string $keyword;

    /** @var string $title */
    protected string $title;

    /** @var string $description */
    protected string $description;

    /** @var string $url */
    protected string $url;

    /** @var string $html */
    protected string $html;

    /** @var string $content */
    protected string $content;

    /** @var Crawler $dom */
    protected Crawler $dom;

    /** @var Collection $headings */
    protected Collection $headings;

    /** @var Collection $images */
    protected Collection $images;

    /** @var Collection $links */
    protected Collection $links;

    /** @var Collection $results */
    protected Collection $results;

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
     * @return self
     * @throws KeywordNotSetException
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

    /**
     * @param string|null $keyword
     * @param string|null $title
     * @param string|null $description
     * @param string|null $html
     * @param string|null $url
     * @return void
     * @throws KeywordNotSetException
     */
    public function prepareData(
        string $keyword = null,
        string $title = null,
        string $description = null,
        string $html = null,
        string $url = null
    ): void
    {
        if (! $keyword) {
            throw new KeywordNotSetException();
        }

        $this->keyword      = str($keyword)->transliterate();
        $this->title        = str($title)->transliterate();
        $this->description  = str($description)->transliterate();
        $this->url          = str(str_replace(['-', '_', '/'], ' ', $url))->transliterate();
        $this->html         = $this->stripUnnecessaryTags($html);
        $this->content      = str($this->html)->stripTags()->squish()->transliterate();

        $this->dom = new Crawler($this->html);

        $this->findDomNodes();
    }

    public function getResults(): Collection
    {
        return $this->results;
    }

    /**
     * Strip HTML & BODY tags
     * @param $html
     * @return string
     * @noinspection HtmlRequiredLangAttribute
     */
    protected function stripUnnecessaryTags($html): string
    {
        if (!$html) {
            return '';
        }
        
        // Remove <head> tag and its content
        $html = preg_replace(
            '/<head(?:\s+[a-z]+\s*=\s*(?:"[^"]*"|\'[^\']*\'|[^\s>]+))*\s*>([\S\s]*)<\/head>/m',
            '',
            $html
        );

        return str_replace(['<html>', '</html>', '<body>', '</body>', '\n', '\t'], " ", $html);
    }

    /**
     * @return Collection
     */
    protected function findHeadingInHtml(): Collection
    {
        $search = preg_match_all('/<(h1|h2|h3|h4|h5|h6)>(.*?)<\/\1>/is', $this->html, $matches);

        return $search > 1 ? collect($matches[2]) : collect([]);
    }

    protected function findDomNodes(): void
    {
        // Sử dụng DomCrawler để tìm các elements
        $this->images = collect($this->dom->filter('img')->each(function (Crawler $node) {
            return $node;
        }));
        
        $this->headings = collect($this->dom->filter('h1,h2,h3,h4,h5,h6')->each(function (Crawler $node) {
            return $node;
        }));
        
        $this->links = collect($this->dom->filter('a')->each(function (Crawler $node) {
            return $node;
        }));
    }

    /**
     * @throws KeywordNotSetException
     */
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

        $this->results = $this->results->concat($result->toArray());

        return $this;
    }

    protected function checkTitleLength(): Analysis
    {
        $checker = new CheckTitleLength($this->title);
        $result = $checker->check()->getResult();

        $this->results = $this->results->concat($result->toArray());

        return $this;
    }

    protected function checkKeywordInTitle(): Analysis
    {
        $checker = new CheckKeywordInTitle($this->keyword, $this->title);
        $result = $checker->check()->getResult();

        $this->results = $this->results->concat($result->toArray());

        return $this;
    }

    protected function checkDescriptionLength(): Analysis
    {
        $checker = new CheckDescriptionLength($this->description);
        $result = $checker->check()->getResult();

        $this->results = $this->results->concat($result->toArray());

        return $this;
    }

    protected function checkKeywordInDescription(): Analysis
    {
        if ($this->description) {
            $checker = new CheckKeywordInDescription($this->keyword, $this->description);
            $result = $checker->check()->getResult();

            $this->results = $this->results->concat($result->toArray());
        }

        return $this;
    }

    protected function checkContentLength(): Analysis
    {
        $checker = new CheckContentLength($this->content);
        $result = $checker->check()->getResult();

        $this->results = $this->results->concat($result->toArray());

        return $this;
    }

    protected function checkKeywordInFirstParagraph(): Analysis
    {
        $checker = new CheckKeywordInFirstParagraph($this->keyword, $this->html);
        $result = $checker->check()->getResult();

        $this->results = $this->results->concat($result->toArray());

        return $this;
    }

    protected function checkHeadingInContent(): Analysis
    {
        if ($this->html) {
            $checker = new CheckHeadingInContent($this->headings);
            $result = $checker->check()->getResult();

            $this->results = $this->results->concat($result->toArray());
        }

        return $this;
    }

    protected function checkKeywordInHeading(): Analysis
    {
        if ($this->html) {
            $checker = new CheckKeywordInHeading($this->keyword, $this->headings);
            $result = $checker->check()->getResult();

            $this->results = $this->results->concat($result->toArray());
        }

        return $this;
    }

    protected function checkImageInContent(): Analysis
    {
        if ($this->html) {
            $checker = new CheckImageInContent($this->images);
            $result = $checker->check()->getResult();

            $this->results = $this->results->concat($result->toArray());
        }

        return $this;
    }

    protected function checkKeywordInImageAlt(): Analysis
    {
        if ($this->images->count() >= 1) {
            $checker = new CheckKeywordInImgAlt($this->images, $this->keyword);
            $result = $checker->check()->getResult();

            $this->results = $this->results->concat($result->toArray());
        }

        return $this;
    }

    protected function checkLinkInContent(): Analysis
    {
        if ($this->html) {
            $checker = new CheckLinkInContent($this->links);
            $result = $checker->check()->getResult();

            $this->results = $this->results->concat($result->toArray());
        }

        return $this;
    }

    protected function checkKeywordInLinkTitle(): Analysis
    {
        if ($this->links->count() > 0) {
            $checker = new CheckKeywordInLinkTitle($this->links, $this->keyword);
            $result = $checker->check()->getResult();

            $this->results = $this->results->concat($result->toArray());
        }

        return $this;
    }

    protected function checkKeywordDensity(): Analysis
    {
        if ($this->html) {
            $checker = new CheckKeywordDensity($this->content, $this->keyword);
            $result = $checker->check()->getResult();

            $this->results = $this->results->concat($result->toArray());
        }

        return $this;
    }

    protected function checkKeywordInUrl(): Analysis
    {
        $checker = new CheckKeywordInUrl($this->url, $this->keyword);
        $result = $checker->check()->getResult();

        $this->results = $this->results->concat($result->toArray());

        return $this;
    }
}
