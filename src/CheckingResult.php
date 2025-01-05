<?php

namespace Qmas\KeywordAnalytics;

use Illuminate\Support\Collection;

class CheckingResult
{
    /** @var Collection $result */
    public static Collection $result;

    public function add(array $data): void
    {
        if (! self::$result instanceof Collection) {
            self::$result = collect();
        }

        self::$result->push($data);
    }
}
