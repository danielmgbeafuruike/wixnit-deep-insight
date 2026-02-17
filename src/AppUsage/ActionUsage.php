<?php

    namespace Wixnit\DeepInsight\AppUsage;

    class ActionUsage
    {
        public function __construct(
            public String $actionName,
            public int $totalCount,
            public float $conversionRate,

            /**
             * a list of 7 days, each day has a count of usage
             * @var int[]
             */
            public array $last7DaysTrend = [],
        ) {}
    }