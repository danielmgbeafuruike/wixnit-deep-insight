<?php

    namespace Wixnit\DeepInsight\Metrics;

    class DeviceMetric
    {
        public function __construct(public string $device = "", public int $value = 0) {}
    }