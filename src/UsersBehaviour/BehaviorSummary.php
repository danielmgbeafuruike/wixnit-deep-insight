<?php

    namespace Wixnit\DeepInsight\UsersBehaviour;

    class BehaviorSummary
    {
        public function __construct(
            public float $nextDayReturnRate,
            public float $retention7Day,
            public float $avgTimeToFirstAction, // minutes
            public float $milestoneCompletionRate,
        ){}
    }