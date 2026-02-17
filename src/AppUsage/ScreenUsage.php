<?php

    namespace Wixnit\DeepInsight\AppUsage;

    class ScreenUsage
    {
        public function __construct(
            public string $screenName,
            public int $totalViews,
            public float $avgDuration,
            public float $bounceRate,

            /**
             * integers that represent the number of views per day
             * @var array<int> $last7DaysTrend
             */
            public array $last7DaysTrend = [],
        ) {}
    }