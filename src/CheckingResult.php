<?php

namespace Qmas\KeywordAnalytics;

use Illuminate\Support\Collection;

class CheckingResult
{
    /** @var Collection $result */
    public static $result;

    public function add(array $data)
    {
        if (! self::$result instanceof Collection) {
            self::$result = collect();
        }

        self::$result->push($data);
    }
}
