<?php

    namespace Wixnit\DeepInsight\AppUsage;

    class AppUsageSummary
    {
        public function __construct(
            public int $dailyActiveUsers = 0,
            public int $weeklyActiveUsers = 0,
            public int $monthlyActiveUsers = 0,
            public float $avgSessionDuration = 0.0,
            public float $returningUsersPercent = 0.0,
            public float $churnRiskPercent = 0.0,
        ) {}
    }