<?php

namespace Qmas\KeywordAnalytics\Abstracts;

abstract class Checker
{
    protected $result;

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