<?php

    namespace Wixnit\DeepInsight\Metrics;

    class CountryMetric
    {
        public function __construct(public string $country = "", public int $value = 0) {}
    }