<?php

namespace Qmas\KeywordAnalytics\Checkers;

use Illuminate\Support\Collection;

abstract class Checker
{
    protected Collection $result;

    public function __construct()
    {
        $this->result = collect();
    }

    public function getResult()
    {
        return $this->result;
    }

    public abstract function check(): Checker;
}