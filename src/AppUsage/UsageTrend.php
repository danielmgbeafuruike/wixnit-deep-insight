<?php

    namespace Wixnit\DeepInsight\AppUsage;

    use Wixnit\Utilities\Date;

    class UsageTrend
    {
        public function __construct(
            public int $date,
            public int $dailyActiveUsers,
            public int $weeklyActiveUsers,
            public int $monthlyActiveUsers,
        ) {}
    }