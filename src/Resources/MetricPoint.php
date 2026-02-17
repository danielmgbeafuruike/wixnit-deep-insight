<?php

    namespace Wixnit\DeepInsight\Resources;

    class MetricPoint
    {
        public function __construct(
            public int $ts, // timestamp
            public float $cpu,
            public float $memory,
            public float $disk,
            public float $rpm,
        ){}
    }