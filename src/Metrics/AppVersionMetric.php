<?php

    namespace Wixnit\DeepInsight\Metrics;

    class AppVersionMetric
    {
        public function __construct(public string $version = "", public int $value = 0, public string $build = "",) {}
    }