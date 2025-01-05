<?php

namespace Qmas\KeywordAnalytics;

use Illuminate\Support\Facades\Facade as BaseFacade;

/**
 * Class Facade
 * @package Qmas\KeywordAnalytics
 *
 * @method static Analysis fromRequest()
 * @method static Analysis run(string $keyword = '', string $title = '', string $description = '', string $html = '', string $url = '')
 */
class Facade extends BaseFacade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return Analysis::class;
    }
}
